// Script to remove hot file if Vite dev server is not running
import fs from 'fs';
import http from 'http';
import { URL } from 'url';

const hotFile = './public/hot';

// Read hot file if it exists
if (fs.existsSync(hotFile)) {
    try {
        const hotUrl = fs.readFileSync(hotFile, 'utf8').trim();
        const url = new URL(hotUrl);
        
        // Try to connect to the Vite dev server
        const request = http.get(url, (res) => {
            // Server is running, keep the file
            process.exit(0);
        });
        
        request.on('error', (err) => {
            // Server is not running, remove the hot file
            fs.unlinkSync(hotFile);
            console.log('Removed hot file - Vite dev server is not running');
            process.exit(0);
        });
        
        request.setTimeout(2000, () => {
            // Timeout - server is not running
            request.destroy();
            fs.unlinkSync(hotFile);
            console.log('Removed hot file - Vite dev server timeout');
            process.exit(0);
        });
    } catch (error) {
        // Invalid hot file, remove it
        fs.unlinkSync(hotFile);
        console.log('Removed invalid hot file');
        process.exit(0);
    }
} else {
    process.exit(0);
}

