import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static values = {
        done: Number,
        total: Number
    }

    connect() {
        const done = this.doneValue || 0
        const total = this.totalValue || 0
        const remaining = Math.max(total - done, 0)

        // Очистка canvas перед створенням (на випадок повторного підключення)
        this.element.getContext('2d').clearRect(0, 0, this.element.width, this.element.height)

        new Chart(this.element, {
            type: 'doughnut',
            data: {
                labels: ['Done', 'Remaining'],
                datasets: [{
                    data: [done, remaining],
                    backgroundColor: ['#22c55e', '#f87171'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // ВАЖЛИВО
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        })
    }
}
