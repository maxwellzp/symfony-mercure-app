import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["value", "icon", "diff"]
    static values = {
        market: String,
        mercureUrl: String
    }
    connect() {
        this.currentPrice = null;
        this.subscribeToMercure();
    }
    subscribeToMercure() {
        this.eventSource = new EventSource(this.mercureUrlValue);

        this.eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data)
            this.updatePrice(data.rate)
        }
    }
    updatePrice(newPrice) {
        if (this.currentPrice === null) {
            this.currentPrice = newPrice;
            this.valueTarget.textContent = newPrice.toLocaleString();
            this.diffTarget.textContent = '';
            return;
        }

        const diff = newPrice - this.currentPrice
        const percent = ((diff / this.currentPrice) * 100).toFixed(2)

        const direction = diff > 0 ? "up" : "down"
        const arrowIcon = direction === "up"
            ? `<i class="bi bi-arrow-up arrow-up"></i>`
            : `<i class="bi bi-arrow-down arrow-down"></i>`

        const formattedDiff = `${diff > 0 ? '+' : ''}${diff.toLocaleString()} (${percent > 0 ? '+' : ''}${percent}%)`


        this.valueTarget.textContent = newPrice.toLocaleString();
        this.iconTarget.innerHTML = arrowIcon;
        this.diffTarget.textContent = formattedDiff;
        this.diffTarget.classList.remove('text-success', 'text-danger');
        this.diffTarget.classList.add(diff > 0 ? 'text-success' : 'text-danger');

        this.element.querySelector('.price-card').classList.remove("price-up", "price-down")
        this.element.querySelector('.price-card').classList.add(`price-${direction}`)

        this.currentPrice = newPrice

        setTimeout(() => {
            this.element.querySelector('.price-card').classList.remove("price-up", "price-down")
        }, 1000)
    }
    disconnect() {
        if (this.eventSource) {
            this.eventSource.close()
        }
    }
}
