// CSS phải import TRƯỚC
import "bootstrap/dist/css/bootstrap.min.css";
import "@fortawesome/fontawesome-free/css/all.min.css";

// Sau đó mới JS
import "./bootstrap";
import Alpine from "alpinejs";
import "./cart";

window.Alpine = Alpine;
Alpine.start();