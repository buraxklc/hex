class ColorApp {
    constructor() {
        this.converter = null;
        this.colorHistory = JSON.parse(localStorage.getItem('colorHistory')) || [];
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadHistory();
        this.setDefaultColor('#ff5733');
    }
    
    bindEvents() {
        // HEX input değişikliği
        document.getElementById('hex-input').addEventListener('input', (e) => {
            this.handleHexInput(e.target.value);
        });
        
        // Color picker değişikliği  
        document.getElementById('color-picker').addEventListener('change', (e) => {
            this.handleColorPicker(e.target.value);
        });
        
        // RGB input değişiklikleri
        ['r-input', 'g-input', 'b-input'].forEach(id => {
            document.getElementById(id).addEventListener('input', () => {
                this.handleRgbInput();
            });
        });
        
        // Copy butonları
        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.copyToClipboard(e.target.closest('.copy-btn').dataset.copy);
            });
        });
        
        // Preset renkler
        document.querySelectorAll('.preset-color').forEach(preset => {
            preset.addEventListener('click', (e) => {
                const hex = e.target.dataset.hex;
                this.setColor(hex);
            });
        });
        
        // History renkler (dinamik)
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('history-color')) {
                const hex = e.target.dataset.hex;
                this.setColor(hex);
            }
        });
    }
    
    async handleHexInput(hex) {
        if (!hex.startsWith('#')) {
            hex = '#' + hex.replace('#', '');
        }
        
        if (this.isValidHex(hex)) {
            document.getElementById('color-picker').value = hex;
            await this.updateColorInfo(hex);
        }
    }
    
    handleColorPicker(hex) {
        document.getElementById('hex-input').value = hex;
        this.updateColorInfo(hex);
    }
    
    async handleRgbInput() {
        const r = parseInt(document.getElementById('r-input').value) || 0;
        const g = parseInt(document.getElementById('g-input').value) || 0;
        const b = parseInt(document.getElementById('b-input').value) || 0;
        
        try {
            const response = await fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=rgb_to_hex&r=${r}&g=${g}&b=${b}`
            });
            
            const result = await response.json();
            if (result.success) {
                const hex = result.data;
                document.getElementById('hex-input').value = hex;
                document.getElementById('color-picker').value = hex;
                await this.updateColorInfo(hex);
            }
        } catch (error) {
            console.error('RGB to HEX conversion error:', error);
        }
    }
    
    async updateColorInfo(hex) {
        try {
            const response = await fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_color_info&hex=${encodeURIComponent(hex)}`
            });
            
            const result = await response.json();
            
            if (result.success) {
                const data = result.data;
                
                // RGB değerlerini güncelle
                document.getElementById('r-input').value = data.rgb.r;
                document.getElementById('g-input').value = data.rgb.g;
                document.getElementById('b-input').value = data.rgb.b;
                
                // HSL değerini güncelle
                document.getElementById('hsl-output').textContent = data.hsl.string;
                
                // Renk önizlemesini güncelle
                document.getElementById('color-display').style.backgroundColor = hex;
                
                // Komplementer rengi göster
                const compColor = document.getElementById('complementary-color');
                compColor.style.backgroundColor = data.complementary;
                compColor.dataset.hex = data.complementary;
                
                // Kontrast değerlerini göster
                document.getElementById('white-contrast').textContent = 
                    data.contrast_white.toFixed(1) + ':1';
                document.getElementById('black-contrast').textContent = 
                    data.contrast_black.toFixed(1) + ':1';
                
                // History'ye ekle
                this.addToHistory(hex);
                
                // Store current values for copying
                this.currentValues = {
                    hex: hex,
                    rgb: `rgb(${data.rgb.r}, ${data.rgb.g}, ${data.rgb.b})`,
                    hsl: data.hsl.string
                };
            }
        } catch (error) {
            console.error('Color info update error:', error);
        }
    }
    
    setColor(hex) {
        document.getElementById('hex-input').value = hex;
        document.getElementById('color-picker').value = hex;
        this.updateColorInfo(hex);
    }
    
    setDefaultColor(hex) {
        this.setColor(hex);
    }
    
    addToHistory(hex) {
        // Eğer renk zaten varsa, eski kaydı sil
        this.colorHistory = this.colorHistory.filter(color => color !== hex);
        
        // Başa ekle
        this.colorHistory.unshift(hex);
        
        // Maksimum 10 renk tut
        if (this.colorHistory.length > 10) {
            this.colorHistory = this.colorHistory.slice(0, 10);
        }
        
        // LocalStorage'a kaydet
        localStorage.setItem('colorHistory', JSON.stringify(this.colorHistory));
        
        // UI'ı güncelle
        this.loadHistory();
    }
    
    loadHistory() {
        const historyContainer = document.getElementById('color-history');
        historyContainer.innerHTML = '';
        
        this.colorHistory.forEach(hex => {
            const colorDiv = document.createElement('div');
            colorDiv.className = 'history-color';
            colorDiv.style.backgroundColor = hex;
            colorDiv.dataset.hex = hex;
            colorDiv.title = hex;
            historyContainer.appendChild(colorDiv);
        });
    }
    
    async copyToClipboard(type) {
        let textToCopy = '';
        
        switch (type) {
            case 'hex':
                textToCopy = this.currentValues?.hex || document.getElementById('hex-input').value;
                break;
            case 'rgb':
                textToCopy = this.currentValues?.rgb || 'rgb(0, 0, 0)';
                break;
            case 'hsl':
                textToCopy = this.currentValues?.hsl || 'hsl(0, 0%, 0%)';
                break;
        }
        
        try {
            await navigator.clipboard.writeText(textToCopy);
            this.showToast(`${type.toUpperCase()} kopyalandı: ${textToCopy}`);
        } catch (err) {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = textToCopy;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            this.showToast(`${type.toUpperCase()} kopyalandı!`);
        }
    }
    
    showToast(message) {
        // Eski toast'ları temizle
        const existingToast = document.querySelector('.toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        // Animasyon için timeout
        setTimeout(() => toast.classList.add('show'), 100);
        
        // 3 saniye sonra kaldır
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    isValidHex(hex) {
        return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
    }
}

// Sayfa yüklendiğinde uygulamayı başlat
document.addEventListener('DOMContentLoaded', () => {
    new ColorApp();
});

// Smooth scroll ve diğer animations
document.addEventListener('DOMContentLoaded', () => {
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Animate cards on scroll
    document.querySelectorAll('.input-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});