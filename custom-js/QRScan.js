class QRScan{
    #onScanInitFailure = null;
    #onScanSuccess = null;
    #onScanFailure = null;
    #scannerContainer = null;
    #qrbox_w = 250;
    #qrbox_h = 250;
    #facing_mode = 'environment';
    #qrCodeReader = null;
    constructor(onScanInitFailure, onScanSuccess, onScanFailure, scannerContainer, qrbox_w=380, qrbox_h=500, camera='back') {
        this.#onScanInitFailure = onScanInitFailure;
        this.#onScanSuccess = onScanSuccess;
        this.#onScanFailure = onScanFailure;
        this.#scannerContainer = scannerContainer;
        this.#qrbox_w = qrbox_w;
        this.#qrbox_h = qrbox_h;
        this.#facing_mode = camera=='front'?'user':'environment';
        this.#qrCodeReader = new Html5Qrcode("qr-reader");
    }

    startQrScanner() {
        this.#scannerContainer.style.display = 'block';
        this.#qrCodeReader.start(
            { facingMode: this.#facing_mode }, // Use the back camera for mobile
            {
                fps: 10,
                qrbox: { width: this.#qrbox_w, height: this.#qrbox_h }
            },
            this.#onScanSuccess,
            this.#onScanFailure
        ).catch(err => {
            this.#scannerContainer.style.display = 'none';
            // console.error(`Error starting QR code scanner: ${err}`);
            this.#onScanInitFailure(err);
        });
    }

    stopQrScanner() {
        this.#qrCodeReader.stop().then(() => {
            this.#qrCodeReader.clear();
            this.#scannerContainer.style.display = 'none';
        }).catch(err => {
            alert('Some error occurred. Please try again.');
            // console.error(`Error stopping QR code scanner: ${err}`);
        });
    }
    
}