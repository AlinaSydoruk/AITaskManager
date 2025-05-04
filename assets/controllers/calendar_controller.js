// assets/controllers/calendar_controller.js
import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["weekLabel", "weekGrid", "unscheduledPanel", "unscheduledList", "toggleButton"];

    static values = {
        tasks: Array,
        unscheduledTasks: Array,
        boardId: String
    };

    connect() {
        this.currentDate = new Date();
        this.constructor.instance = this;
        window.calendarController = this;
        this.renderWeek();
    }

    prevWeek() {
        this.currentDate.setDate(this.currentDate.getDate() - 7);
        this.renderWeek();
    }

    nextWeek() {
        this.currentDate.setDate(this.currentDate.getDate() + 7);
        this.renderWeek();
    }

    toggleUnscheduled() {
        const panel = this.unscheduledPanelTarget;
        const button = this.toggleButtonTarget;

        const isHidden = panel.hasAttribute("hidden");

        if (isHidden) {
            panel.removeAttribute("hidden");
            button.classList.add("hidden"); // Скрываем кнопку
            this.renderUnscheduled();
        } else {
            panel.setAttribute("hidden", "true");
            button.classList.remove("hidden"); // Показываем кнопку
        }
    }
    closeUnscheduled() {
        this.unscheduledPanelTarget.setAttribute("hidden", "true");
        this.toggleButtonTarget.classList.remove("hidden");
    }



    renderUnscheduled() {
        this.unscheduledListTarget.innerHTML = this.unscheduledTasksValue.map((t, index) => `
        <li class="bg-gray-100 rounded p-2 shadow-sm cursor-move"
            draggable="true"
            data-task-index="${index}"
            ondragstart="event.dataTransfer.setData('taskIndex', ${index})">
            <div class="font-semibold">${t.title}</div>
            <div class="text-xs text-gray-500">${t.estimateMinutes} min</div>
        </li>
    `).join('');
    }



    renderWeek() {
        const startOfWeek = new Date(this.currentDate);
        startOfWeek.setDate(this.currentDate.getDate() - this.currentDate.getDay());

        const days = [];
        const options = { day: 'numeric', month: 'short' };
        for (let i = 0; i < 7; i++) {
            const day = new Date(startOfWeek);
            day.setDate(day.getDate() + i);
            days.push(day);
        }

        this.weekLabelTarget.textContent = `${days[0].toLocaleDateString()} - ${days[6].toLocaleDateString()}`;

        const tasks = this.tasksValue;
        let gridHtml = '';

        // Временная шкала
        let hours = `<div class="h-10"></div>`;
        for (let hour = 7; hour <= 20; hour++) {
            const label = hour < 12 ? `${hour} AM` : hour === 12 ? `12 PM` : `${hour - 12} PM`;
            hours += `<div class="h-16 border-b text-right pr-2 text-xs pt-1">${label}</div>`;
        }
        gridHtml += `<div class="bg-white sticky left-0 z-10">${hours}</div>`;

        // Колонки по дням
        for (const day of days) {
            const dayISO = day.toISOString().split('T')[0];
            const dayTasks = tasks.filter(task => task.scheduledFor && task.scheduledFor.startsWith(dayISO));

            let column = `
            <div class="h-10 flex items-center justify-center text-xs text-gray-600 border-b bg-white sticky top-0 z-10">
                ${day.toLocaleDateString(undefined, options)}
            </div>
        `;

            for (let hour = 7; hour <= 20; hour++) {
                const hourTasks = dayTasks.filter(task => {
                    const taskHour = new Date(task.scheduledFor).getHours();
                    return taskHour === hour;
                });

                column += `<div class="h-16 border-b relative"
    ondragover="event.preventDefault()"
    ondrop="window.handleDrop(event, '${dayISO}', ${hour})">`;


                column += hourTasks.map(t => {
                    const start = new Date(t.scheduledFor);
                    const duration = t.estimateMinutes || 30;
                    const end = new Date(start.getTime() + duration * 60000);

                    const startStr = start.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
                    const endStr = end.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });

                    const startMinutes = start.getMinutes();
                    const top = Math.floor((startMinutes / 60) * 64);
                    const height = Math.max(20, (duration / 60) * 64);

                    return `
   <div
        class="absolute left-1 right-1 bg-green-200 text-gray-900 text-xs rounded px-2 py-1 shadow-sm calendar-task"
        draggable="true"
        data-scheduled-task-id="${t.id}"
        style="top: ${top}px; height: ${height}px; cursor: pointer;"
        onclick="window.location.href='/board/task/' + ${t.id} + '?boardId=' + ${t.boardId}"
    >
        <div class="font-semibold">${t.title}</div>
        <div class="opacity-80 text-[10px]">${startStr} – ${endStr}</div>
    </div>
`;

                }).join('');

                column += `</div>`;
            }

            gridHtml += `<div class="border-l text-left bg-white">${column}</div>`;
        }

        this.weekGridTarget.innerHTML = gridHtml;
        // навешиваем dragstart на таски внутри календаря
        this.weekGridTarget.querySelectorAll(".calendar-task").forEach(el => {
            el.addEventListener("dragstart", e => {
                const id = el.dataset.scheduledTaskId;
                e.dataTransfer.setData("scheduledTaskId", id);
            });
        });
    }

}
window.handleDrop = async (event, dayISO, hour) => {
    const controller = window.calendarController;

    const unscheduledIndex = event.dataTransfer.getData('taskIndex');
    const scheduledTaskId = event.dataTransfer.getData('scheduledTaskId');

    const scheduledDate = new Date(`${dayISO}T${hour.toString().padStart(2, '0')}:00`);
    const scheduledFor = `${dayISO}T${scheduledDate.getHours().toString().padStart(2, '0')}:00:00`;

    let task;

    if (unscheduledIndex !== '') {
        // Перетаскивание из панели
        task = controller.unscheduledTasksValue[unscheduledIndex];
        task.scheduledFor = scheduledFor;

        controller.tasksValue = [...controller.tasksValue, task];
        controller.unscheduledTasksValue = controller.unscheduledTasksValue.filter((_, i) => i != unscheduledIndex);
    }  else if (scheduledTaskId !== '') {
    const index = controller.tasksValue.findIndex(t => t.id == scheduledTaskId);
    if (index === -1) return;

    const updatedTask = { ...controller.tasksValue[index], scheduledFor };
    controller.tasksValue = [
        ...controller.tasksValue.slice(0, index),
        updatedTask,
        ...controller.tasksValue.slice(index + 1)
    ];

    task = updatedTask;
}

    try {
        await fetch(`/dashboard/task/${task.id}/schedule`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ scheduledFor })
        });
    } catch (e) {
        console.error("Failed to update task:", e);
    }

    controller.renderWeek();
    controller.renderUnscheduled();
};
