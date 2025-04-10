import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["tbody"];
    static values = {
        mercureUrl: String
    }
    connect() {
        this.maxRows = 10;
        this.subscribeToMercure();
    }

    subscribeToMercure() {
        this.eventSource = new EventSource(this.mercureUrlValue);

        this.eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data)
            this.addRow(data.market, data.rate, data.timestamp);
        }
    }

    addRow(market, rate, $timestamp) {
        const row = document.createElement("tr")
        row.innerHTML = `
    <td>${market}</td>
    <td>${rate.toLocaleString()}</td>
    <td>${$timestamp}</td>
  `
        this.tbodyTarget.prepend(row)

        while (this.tbodyTarget.rows.length > this.maxRows) {
            this.tbodyTarget.deleteRow(-1)
        }
    }



    disconnect() {
        if (this.eventSource) {
            this.eventSource.close()
        }
    }
}