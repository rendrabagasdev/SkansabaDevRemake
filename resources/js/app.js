// Livewire already includes Alpine.js and persist plugin
// No need to import Alpine separately

// Import Croppie for image cropping component
import Croppie from "croppie";

// Make Croppie globally available for Alpine components
window.Croppie = Croppie;

// Debug: Verify Croppie is loaded
console.log("Croppie loaded:", typeof Croppie !== "undefined");
