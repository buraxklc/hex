<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ColorStudio</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #1e1e1e;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            line-height: 1.5;
        }

        .studio {
            background: #2c2c2c;
            border: 1px solid #3e3e3e;
            border-radius: 12px;
            width: 100%;
            max-width: 360px;
            overflow: hidden;
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.5);
        }

        .header {
            padding: 20px 24px;
            border-bottom: 1px solid #3e3e3e;
            background: #2a2a2a;
        }

        .title {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 13px;
            color: #a0a0a0;
            font-weight: 400;
        }

        .content {
            padding: 24px;
        }

        .section {
            margin-bottom: 24px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section-label {
            font-size: 12px;
            font-weight: 500;
            color: #b0b0b0;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hex-input {
            width: 100%;
            background: #1a1a1a;
            border: 1px solid #404040;
            border-radius: 6px;
            padding: 12px 16px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'SF Mono', Monaco, monospace;
            transition: all 0.15s ease;
        }

        .hex-input:focus {
            outline: none;
            border-color: #0066ff;
            background: #1f1f1f;
        }

        .hex-input::placeholder {
            color: #666;
        }

        .color-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 6px;
            background: #1f1f1f;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #333;
        }

        .color-dot {
            aspect-ratio: 1;
            border-radius: 4px;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.15s ease;
            position: relative;
        }

        .color-dot:hover {
            transform: scale(1.15);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .color-dot.selected {
            border: 2px solid #0066ff;
            transform: scale(1.1);
        }

        .color-dot.selected::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: #0066ff;
            border-radius: 50%;
            border: 2px solid #ffffff;
        }

        .preview {
            width: 100%;
            height: 100px;
            border-radius: 8px;
            border: 1px solid #333;
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
            overflow: hidden;
        }

        .preview:hover {
            border-color: #555;
        }

        .preview::after {
            content: 'Click to copy';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        .preview:hover::after {
            opacity: 1;
        }

        .color-values {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .value-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1f1f1f;
            border: 1px solid #333;
            border-radius: 6px;
            padding: 10px 12px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .value-row:hover {
            background: #252525;
            border-color: #404040;
        }

        .value-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .value-label {
            font-size: 11px;
            color: #888;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .value-text {
            font-size: 13px;
            color: #ffffff;
            font-weight: 500;
            font-family: 'SF Mono', Monaco, monospace;
        }

        .copy-icon {
            font-size: 14px;
            color: #666;
            transition: color 0.15s ease;
        }

        .value-row:hover .copy-icon {
            color: #0066ff;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #0066ff;
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 8px 24px rgba(0, 102, 255, 0.4);
            transform: translateX(400px);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
        }

        .notification.show {
            transform: translateX(0);
        }

        @media (max-width: 480px) {
            .studio {
                max-width: 320px;
            }
            
            .content {
                padding: 20px;
            }
            
            .color-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="studio">
        <div class="header">
            <div class="title">Color Studio</div>
            <div class="subtitle">Professional color picker</div>
        </div>
        
        <div class="content">
            <div class="section">
                <div class="section-label">HEX Value</div>
                <input type="text" id="hexInput" class="hex-input" placeholder="#3B82F6" maxlength="7">
            </div>

            <div class="section">
                <div class="section-label">Palette</div>
                <div class="color-grid" id="colorGrid"></div>
            </div>

            <div class="section">
                <div class="section-label">Preview</div>
                <div class="preview" id="preview"></div>
            </div>

            <div class="section">
                <div class="section-label">Values</div>
                <div class="color-values">
                    <div class="value-row" data-format="hex">
                        <div class="value-info">
                            <div class="value-label">HEX</div>
                            <div class="value-text" id="hexValue">#3B82F6</div>
                        </div>
                        <div class="copy-icon">⌘C</div>
                    </div>

                    <div class="value-row" data-format="rgb">
                        <div class="value-info">
                            <div class="value-label">RGB</div>
                            <div class="value-text" id="rgbValue">59, 130, 246</div>
                        </div>
                        <div class="copy-icon">⌘C</div>
                    </div>

                    <div class="value-row" data-format="hsl">
                        <div class="value-info">
                            <div class="value-label">HSL</div>
                            <div class="value-text" id="hslValue">217°, 91%, 60%</div>
                        </div>
                        <div class="copy-icon">⌘C</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const palette = [
            '#FF5733', '#33FF57', '#3357FF', '#FF33F5', '#F5FF33', '#33FFF5',
            '#FF8C33', '#8C33FF', '#33FF8C', '#FF3388', '#88FF33', '#3388FF',
            '#FFB833', '#B833FF', '#33FFB8', '#FF3366', '#66FF33', '#3366FF',
            '#FFC733', '#C733FF', '#33FFC7', '#FF3344', '#44FF33', '#3344FF',
            '#E74C3C', '#3498DB', '#2ECC71', '#F39C12', '#9B59B6', '#1ABC9C',
            '#E67E22', '#34495E', '#95A5A6', '#16A085', '#27AE60', '#2980B9',
            '#8E44AD', '#2C3E50', '#F1C40F', '#E74C3C', '#ECF0F1', '#BDC3C7'
        ];

        class ColorStudio {
            constructor() {
                this.currentColor = '#3B82F6';
                this.elements = {
                    hexInput: document.getElementById('hexInput'),
                    colorGrid: document.getElementById('colorGrid'),
                    preview: document.getElementById('preview'),
                    hexValue: document.getElementById('hexValue'),
                    rgbValue: document.getElementById('rgbValue'),
                    hslValue: document.getElementById('hslValue')
                };
                this.init();
            }

            init() {
                this.createPalette();
                this.bindEvents();
                this.updateColor('#3B82F6');
            }

            createPalette() {
                palette.forEach(color => {
                    const dot = document.createElement('div');
                    dot.className = 'color-dot';
                    dot.style.backgroundColor = color;
                    dot.dataset.color = color;
                    dot.addEventListener('click', () => {
                        this.selectColor(dot, color);
                        this.updateColor(color);
                    });
                    this.elements.colorGrid.appendChild(dot);
                });
            }

            selectColor(selectedDot, color) {
                document.querySelectorAll('.color-dot').forEach(dot => {
                    dot.classList.remove('selected');
                });
                selectedDot.classList.add('selected');
                this.elements.hexInput.value = color.toUpperCase();
            }

            bindEvents() {
                this.elements.hexInput.addEventListener('input', (e) => {
                    let value = e.target.value;
                    if (!value.startsWith('#')) {
                        value = '#' + value;
                        e.target.value = value;
                    }
                    
                    if (this.isValidHex(value)) {
                        this.updateColor(value);
                        this.selectColorFromValue(value);
                    }
                });

                document.querySelectorAll('.value-row').forEach(row => {
                    row.addEventListener('click', () => {
                        const format = row.dataset.format;
                        const value = row.querySelector('.value-text').textContent;
                        const copyValue = format === 'hex' ? value : 
                                        format === 'rgb' ? `rgb(${value})` : 
                                        `hsl(${value})`;
                        this.copyToClipboard(copyValue, format);
                    });
                });

                this.elements.preview.addEventListener('click', () => {
                    this.copyToClipboard(this.currentColor, 'hex');
                });
            }

            selectColorFromValue(color) {
                const dot = document.querySelector(`[data-color="${color.toLowerCase()}"]`);
                if (dot) {
                    document.querySelectorAll('.color-dot').forEach(d => d.classList.remove('selected'));
                    dot.classList.add('selected');
                }
            }

            updateColor(color) {
                this.currentColor = color;
                const rgb = this.hexToRgb(color);
                const hsl = this.hexToHsl(color);

                if (rgb && hsl) {
                    this.elements.preview.style.backgroundColor = color;
                    this.elements.hexValue.textContent = color.toUpperCase();
                    this.elements.rgbValue.textContent = `${rgb.r}, ${rgb.g}, ${rgb.b}`;
                    this.elements.hslValue.textContent = `${hsl.h}°, ${hsl.s}%, ${hsl.l}%`;
                }
            }

            hexToRgb(hex) {
                const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                return result ? {
                    r: parseInt(result[1], 16),
                    g: parseInt(result[2], 16),
                    b: parseInt(result[3], 16)
                } : null;
            }

            hexToHsl(hex) {
                const rgb = this.hexToRgb(hex);
                if (!rgb) return null;

                let {r, g, b} = rgb;
                r /= 255; g /= 255; b /= 255;

                const max = Math.max(r, g, b);
                const min = Math.min(r, g, b);
                let h, s, l = (max + min) / 2;

                if (max === min) {
                    h = s = 0;
                } else {
                    const d = max - min;
                    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                    switch (max) {
                        case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
                        case g: h = ((b - r) / d + 2) / 6; break;
                        case b: h = ((r - g) / d + 4) / 6; break;
                    }
                }

                return {
                    h: Math.round(h * 360),
                    s: Math.round(s * 100),
                    l: Math.round(l * 100)
                };
            }

            isValidHex(hex) {
                return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
            }

            async copyToClipboard(text, format) {
                try {
                    await navigator.clipboard.writeText(text);
                    this.showNotification(`${format.toUpperCase()} copied`);
                } catch (err) {
                    this.showNotification('Copy failed');
                }
            }

            showNotification(message) {
                const existing = document.querySelector('.notification');
                if (existing) existing.remove();

                const notification = document.createElement('div');
                notification.className = 'notification';
                notification.textContent = message;

                document.body.appendChild(notification);
                requestAnimationFrame(() => {
                    notification.classList.add('show');
                });

                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 300);
                }, 2000);
            }
        }

        new ColorStudio();
    </script>
</body>
</html>