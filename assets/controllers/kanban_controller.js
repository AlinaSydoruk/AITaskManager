import { Controller } from '@hotwired/stimulus'
import Sortable from 'sortablejs'

export default class extends Controller {
    static targets = ['column']

    connect() {
        this.columnTargets.forEach(column => {
            Sortable.create(column, {
                group: 'kanban',
                animation: 150,
                onEnd: (event) => {
                    const taskId = event.item.dataset.taskId
                    const newStatus = event.to.dataset.status

                    fetch(`task/${taskId}/update-status`, {

                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ status: newStatus })
                    }).then(() => {
                        event.item.classList.add('bg-green-50')
                        setTimeout(() => event.item.classList.remove('bg-green-50'), 500)
                    })
                }
            })
        })
    }
}
