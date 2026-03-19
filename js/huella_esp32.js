class HuellaESP32 {
    constructor() {
        this.port = null;
        this.reader = null;
        this.writer = null;
        this.connecting = false;
    }

    async connect() {
        try {
            if (!navigator.serial) {
                throw new Error('Web Serial API no soportada (usa Chrome/Edge)');
            }

            // Request puerto USB ESP32
            this.port = await navigator.serial.requestPort({ filters: [] });
            
            await this.port.open({ baudRate: 115200 });
            
            const reader = this.port.readable.getReader();
            this.reader = reader;

            // Auto-read huella loop
            while (true) {
                const { value, done } = await reader.read();
                if (done) break;

                const data = new TextDecoder().decode(value);
                this.onHuellaRecibida(data);
            }
        } catch (error) {
            console.error('ESP32 Error:', error);
            Swal.fire('ESP32', 'Conecta lector huella USB', 'info');
        }
    }

    async onHuellaRecibida(data) {
        try {
            console.log('ESP32 data:', data);
            const huella = JSON.parse(data);
            
            if (huella.valida && huella.votante_id) {
                Swal.fire({
                    title: '✅ Huella VÁLIDA',
                    text: `Votante ID: ${huella.votante_id}`,
                    icon: 'success',
                    timer: 1500
                });
            }
        } catch (e) {
            console.log('Raw ESP32:', data);
        }
    }

    disconnect() {
        if (this.reader) this.reader.cancel();
        if (this.port) this.port.close();
    }
}

// Auto-connect al cargar página
const huella = new HuellaESP32();
document.addEventListener('DOMContentLoaded', () => {
    // Conectar ESP32 al cargar
    huella.connect();
    
    // Reconectar si desconecta
    window.addEventListener('beforeunload', () => huella.disconnect());
});

