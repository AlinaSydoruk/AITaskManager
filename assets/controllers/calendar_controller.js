// assets/controllers/calendar_controller.js
import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["weekLabel", "weekGrid", "unscheduledPanel", "unscheduledList", "toggleButton"];

    static values = {
        tasks: Array,
        unscheduledTasks: Array
    };

    connect() {
        this.currentDate = new Date();
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
        this.unscheduledListTarget.innerHTML = this.unscheduledTasksValue.map(t => `
            <li class="bg-gray-100 rounded p-2 shadow-sm">
                <div class="font-semibold">${t.title}</div>
                <div class="text-xs text-gray-500">${t.estimateMinutes} min</div>
            </li>
        `).join('');
    }

    renderWeek() {
        const startOfWeek = new Date(this.currentDate);
        startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());

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

        // 1. Временная шкала
        let hours = '';
        hours += `<div class="h-10"></div>`; // под заголовок
        for (let hour = 7; hour <= 20; hour++) {
            const label = hour < 12 ? `${hour} AM` : hour === 12 ? `12 PM` : `${hour - 12} PM`;
            hours += `<div class="h-16 border-b text-right pr-2 text-xs pt-1">${label}</div>`;
        }
        gridHtml += `<div class="bg-white sticky left-0 z-10">${hours}</div>`;

        // 2. Колонки по дням
        for (const day of days) {
            const dayISO = day.toISOString().split('T')[0];
            const dayTasks = tasks.filter(task => task.dueDate && task.dueDate.startsWith(dayISO));

            let column = '';
            column += `
    <div class="h-10 flex items-center justify-center text-xs text-gray-600 border-b bg-white sticky top-0 z-10">
        ${day.toLocaleDateString(undefined, options)}
    </div>
`;

            for (let hour = 7; hour <= 20; hour++) {
                const hourTasks = dayTasks.filter(task => {
                    const taskHour = new Date(task.dueDate).getHours();
                    return taskHour === hour;
                });

                column += `<div class="h-16 border-b relative">`;

                column += hourTasks.map(t => `
                <div class="absolute top-1 left-1 right-1 bg-blue-100 text-blue-900 text-xs rounded px-1 py-0.5">
                    ${t.title}
                </div>
            `).join('');

                column += `</div>`;
            }

            gridHtml += `<div class="border-l text-left bg-white">${column}</div>`;
        }

        this.weekGridTarget.innerHTML = gridHtml;
    }

}
