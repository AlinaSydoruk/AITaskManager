import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["task", "taskContainer"]

    connect() {
        this.taskTargets.forEach(task => {
            task.addEventListener('dragstart', this.handleDragStart.bind(this))
        })

        this.taskContainerTargets.forEach(container => {
            container.addEventListener('dragover', e => e.preventDefault())
            container.addEventListener('drop', this.handleDrop.bind(this))
        })
    }

    handleDragStart(event) {
        event.dataTransfer.setData("text/plain", event.target.dataset.taskId)
    }

    handleDrop(event) {
        const taskId = event.dataTransfer.getData("text/plain")
        const newStatus = event.currentTarget.dataset.status

        fetch(`/task/${taskId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ status: newStatus })
        }).then(() => window.location.reload())
    }
}
