const { contextBridge, ipcRenderer } = require('electron');

console.log('Preload script is running');

// Expose protected methods that allow the renderer process to use
// the ipcRenderer without exposing the entire object
contextBridge.exposeInMainWorld(
  'electronAPI', {
    minimizeWindow: () => {
      console.log('Preload: minimizeWindow called');
      ipcRenderer.send('window-minimize');
    },
    maximizeWindow: () => {
      console.log('Preload: maximizeWindow called');
      ipcRenderer.send('window-maximize');
    },
    closeWindow: () => {
      console.log('Preload: closeWindow called');
      ipcRenderer.send('window-close');
    },
  }
);

// Log when the API is exposed
console.log('electronAPI exposed to window:', !!window.electronAPI); 