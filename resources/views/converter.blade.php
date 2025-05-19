<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Converter</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px 0;
        }
        
        .logo-container {
            margin-bottom: 20px;
        }
        
        .logo {
            width: 70px;
            height: 70px;
            background-color: #666;
            border-radius: 50%;
        }
        
        .converter-container {
            background-color: #d9d9d9;
            width: 800px;
            max-width: 90%;
            padding: 40px;
            border-radius: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .platform-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .platform-btn {
            padding: 8px 20px;
            border: none;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        
        .youtube-btn {
            background-color: #ff0000;
        }
        
        .facebook-btn {
            background-color: #a7bdea;
            color: #333;
        }
        
        .tiktok-btn {
            background-color: #999;
        }
        
        .format-container {
            display: flex;
            margin-bottom: 20px;
            border-radius: 20px;
            overflow: hidden;
        }
        
        .format-btn {
            padding: 8px 30px;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        
        .mp4-btn {
            background-color: #ff0000;
        }
        
        .mp3-btn {
            background-color: #ffaaaa;
        }
        
        .input-container {
            display: flex;
            width: 100%;
            margin-bottom: 20px;
            gap: 10px;
        }
        
        .url-input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            outline: none;
        }
        
        .convert-btn {
            background-color: #ff0000;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .convert-btn:hover {
            background-color: #cc0000;
        }
        
        .footer-text {
            width: 100%;
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }
        
        .footer-text h2 {
            margin-bottom: 5px;
            font-size: 20px;
        }
        
        .footer-text p {
            margin-top: 0;
        }

        /* History feature styles */
        .history-container {
            width: 800px;
            max-width: 90%;
            margin-top: 30px;
            background-color: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .history-header h3 {
            margin: 0;
            color: #333;
        }
        
        .clear-history-btn {
            background-color: #f0f0f0;
            border: none;
            border-radius: 15px;
            padding: 5px 15px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.3s;
        }
        
        .clear-history-btn:hover {
            background-color: #e0e0e0;
        }
        
        .history-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }
        
        .history-item:hover {
            background-color: #f9f9f9;
        }
        
        .history-item:last-child {
            border-bottom: none;
        }
        
        .history-item-info {
            flex: 1;
        }
        
        .history-item-title {
            font-weight: bold;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 400px;
        }
        
        .history-item-details {
            display: flex;
            font-size: 12px;
            color: #777;
        }
        
        .history-item-platform, .history-item-format {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }
        
        .history-item-platform span, .history-item-format span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .history-item-date {
            margin-left: auto;
        }
        
        .history-item-actions {
            display: flex;
            gap: 10px;
        }
        
        .history-action-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #555;
            font-size: 14px;
            display: flex;
            align-items: center;
            padding: 5px;
            border-radius: 4px;
        }
        
        .history-action-btn:hover {
            background-color: #f0f0f0;
        }
        
        .no-history {
            text-align: center;
            padding: 30px;
            color: #999;
        }
        
        .history-toggle {
            background-color: transparent;
            border: none;
            color: #555;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            padding: 8px 15px;
            border-radius: 15px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        
        .history-toggle:hover {
            background-color: #f0f0f0;
        }
        
        .history-toggle-icon {
            display: inline-block;
            margin-right: 5px;
            transition: transform 0.3s;
        }
        
        .history-toggle.collapsed .history-toggle-icon {
            transform: rotate(-90deg);
        }

        @media (max-width: 600px) {
            .converter-container {
                padding: 20px;
            }
            
            .input-container {
                flex-direction: column;
            }
            
            .convert-btn {
                width: 100%;
            }
            
            .history-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .history-item-actions {
                margin-top: 10px;
                width: 100%;
                justify-content: flex-end;
            }
            
            .history-item-title {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <div class="logo"></div>
    </div>
    
    <div class="converter-container">
        <div class="platform-buttons">
            <button class="platform-btn youtube-btn">Youtube</button>
            <button class="platform-btn facebook-btn">Facebook</button>
            <button class="platform-btn tiktok-btn">Tiktok</button>
        </div>
        
        <div class="format-container">
            <button class="format-btn mp4-btn" id="mp4-btn">MP4</button>
            <button class="format-btn mp3-btn" id="mp3-btn">MP3</button>
        </div>
        
        <div class="input-container">
            <input type="text" class="url-input" placeholder="Search or paste Youtube link here">
            <button class="convert-btn">Convert</button>
        </div>
    </div>
    
    <button class="history-toggle">
        <span class="history-toggle-icon">▼</span> Conversion History
    </button>
    
    <div class="history-container">
        <div class="history-header">
            <h3>Recent Conversions</h3>
            <button class="clear-history-btn">Clear All</button>
        </div>
        <div class="history-list" id="history-list">
            <div class="no-history">No conversion history yet</div>
        </div>
    </div>
    
    <div class="footer-text">
        <h2>Your trusted media converter</h2>
        <p>Convert and download Youtube videos in MP3, MP4, 3GP formats for free</p>
    </div>

    <script>
        // Theme colors for each platform
        const platformThemes = {
            'Youtube': {
                main: '#ff0000', // Red
                secondary: '#ffdddd',
                convertBtnColor: '#ff0000',
                formatBtnColors: {
                    mp4: '#ff0000',
                    mp3: '#ffaaaa'
                },
                indicatorColor: '#ff0000'
            },
            'Facebook': {
                main: '#1877f2', // Facebook Blue
                secondary: '#e4f0fd',
                convertBtnColor: '#1877f2',
                formatBtnColors: {
                    mp4: '#1877f2',
                    mp3: '#8bb8f8'
                },
                indicatorColor: '#1877f2'
            },
            'Tiktok': {
                main: '#010101', // TikTok Black
                secondary: '#e6e6e6',
                convertBtnColor: '#ff0050', // TikTok Pink
                formatBtnColors: {
                    mp4: '#010101',
                    mp3: '#6c6c6c'
                },
                indicatorColor: '#ff0050'
            }
        };
        
        let currentPlatform = 'Youtube';
        let currentFormat = 'mp4';
        
        // Apply theme based on platform
        function applyTheme(platform) {
            const theme = platformThemes[platform];
            const convertBtn = document.querySelector('.convert-btn');
            const mp4Btn = document.getElementById('mp4-btn');
            const mp3Btn = document.getElementById('mp3-btn');
            
            // Update convert button
            convertBtn.style.backgroundColor = theme.convertBtnColor;
            
            // Update format buttons
            mp4Btn.style.backgroundColor = theme.formatBtnColors.mp4;
            mp3Btn.style.backgroundColor = theme.formatBtnColors.mp3;
            
            // Update platform button highlights
            document.querySelectorAll('.platform-btn').forEach(btn => {
                if (btn.textContent === platform) {
                    btn.style.opacity = '1';
                    btn.style.boxShadow = '0 0 5px rgba(0,0,0,0.3)';
                } else {
                    btn.style.opacity = '0.7';
                    btn.style.boxShadow = 'none';
                }
            });

            // Additional visual cues
            document.querySelector('.converter-container').style.borderColor = theme.main;
        }
        
        // Platform button selection
        const platformButtons = document.querySelectorAll('.platform-btn');
        platformButtons.forEach(button => {
            button.addEventListener('click', () => {
                currentPlatform = button.textContent;
                
                // Update placeholder based on selected platform
                const input = document.querySelector('.url-input');
                if (currentPlatform === 'Youtube') {
                    input.placeholder = 'Search or paste Youtube link here';
                } else if (currentPlatform === 'Facebook') {
                    input.placeholder = 'Paste Facebook video link here';
                } else if (currentPlatform === 'Tiktok') {
                    input.placeholder = 'Paste TikTok video link here';
                }
                
                // Apply new theme
                applyTheme(currentPlatform);
            });
        });
        
        // Format button selection
        const formatButtons = document.querySelectorAll('.format-btn');
        formatButtons.forEach(button => {
            button.addEventListener('click', () => {
                currentFormat = button.textContent.toLowerCase();
                
                formatButtons.forEach(btn => {
                    if (btn === button) {
                        btn.style.opacity = '1';
                        btn.style.fontWeight = 'bold';
                    } else {
                        btn.style.opacity = '0.7';
                        btn.style.fontWeight = 'normal';
                    }
                });
            });
        });
        
        // History functionality
        let conversionHistory = JSON.parse(localStorage.getItem('conversionHistory')) || [];
        
        function saveToHistory(url, platform, format) {
            // Extract video title from URL (in a real app, you would get this from API)
            let videoTitle = url;
            
            // Try to extract YouTube video ID
            if (platform === 'Youtube') {
                const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
                if (match) {
                    videoTitle = `YouTube Video (ID: ${match[1]})`;
                }
            } else if (platform === 'Facebook') {
                videoTitle = 'Facebook Video';
            } else if (platform === 'Tiktok') {
                videoTitle = 'TikTok Video';
            }
            
            const conversion = {
                id: Date.now(),
                url: url,
                title: videoTitle,
                platform: platform,
                format: format,
                date: new Date().toISOString()
            };
            
            conversionHistory.unshift(conversion);
            
            // Limit history to 10 items
            if (conversionHistory.length > 10) {
                conversionHistory = conversionHistory.slice(0, 10);
            }
            
            // Save to localStorage
            localStorage.setItem('conversionHistory', JSON.stringify(conversionHistory));
            
            // Update UI
            renderHistory();
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        
        function renderHistory() {
            const historyList = document.getElementById('history-list');
            
            if (conversionHistory.length === 0) {
                historyList.innerHTML = '<div class="no-history">No conversion history yet</div>';
                return;
            }
            
            let html = '';
            
            conversionHistory.forEach(item => {
                const theme = platformThemes[item.platform];
                
                html += `
                <div class="history-item" data-id="${item.id}">
                    <div class="history-item-info">
                        <div class="history-item-title" title="${item.url}">${item.title}</div>
                        <div class="history-item-details">
                            <div class="history-item-platform">
                                <span style="background-color: ${theme.indicatorColor};"></span>
                                ${item.platform}
                            </div>
                            <div class="history-item-format">
                                <span style="background-color: ${item.format === 'mp4' ? theme.formatBtnColors.mp4 : theme.formatBtnColors.mp3};"></span>
                                ${item.format.toUpperCase()}
                            </div>
                            <div class="history-item-date">${formatDate(item.date)}</div>
                        </div>
                    </div>
                    <div class="history-item-actions">
                        <button class="history-action-btn history-reuse-btn" title="Use this URL again">↺ Reuse</button>
                        <button class="history-action-btn history-remove-btn" title="Remove from history">×</button>
                    </div>
                </div>
                `;
            });
            
            historyList.innerHTML = html;
            
            // Add event listeners to action buttons
            document.querySelectorAll('.history-reuse-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = parseInt(e.target.closest('.history-item').dataset.id);
                    const item = conversionHistory.find(i => i.id === id);
                    
                    if (item) {
                        // Set platform
                        const platformBtn = document.querySelector(`.${item.platform.toLowerCase()}-btn`);
                        if (platformBtn) platformBtn.click();
                        
                        // Set format
                        const formatBtn = document.querySelector(`#${item.format}-btn`);
                        if (formatBtn) formatBtn.click();
                        
                        // Set URL
                        document.querySelector('.url-input').value = item.url;
                        
                        // Scroll to converter
                        document.querySelector('.converter-container').scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
            
            document.querySelectorAll('.history-remove-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = parseInt(e.target.closest('.history-item').dataset.id);
                    
                    // Remove from array
                    conversionHistory = conversionHistory.filter(item => item.id !== id);
                    
                    // Save to localStorage
                    localStorage.setItem('conversionHistory', JSON.stringify(conversionHistory));
                    
                    // Update UI
                    renderHistory();
                });
            });
        }
        
        // Clear history button
        document.querySelector('.clear-history-btn').addEventListener('click', () => {
            if (confirm('Are you sure you want to clear all conversion history?')) {
                conversionHistory = [];
                localStorage.setItem('conversionHistory', JSON.stringify(conversionHistory));
                renderHistory();
            }
        });
        
        // Toggle history visibility
        const historyToggle = document.querySelector('.history-toggle');
        const historyContainer = document.querySelector('.history-container');
        
        historyToggle.addEventListener('click', () => {
            historyToggle.classList.toggle('collapsed');
            historyContainer.style.display = historyToggle.classList.contains('collapsed') ? 'none' : 'block';
        });
        
        // Convert button click handler
        document.querySelector('.convert-btn').addEventListener('click', function() {
            const url = document.querySelector('.url-input').value.trim();
            if (!url) {
                alert('Please enter a valid URL');
                return;
            }
            
            // In a real app, you would send this to your backend
            this.textContent = 'Converting...';
            this.disabled = true;
            
            // Simulate conversion process
            setTimeout(() => {
                alert(`Your ${currentFormat.toUpperCase()} conversion from ${currentPlatform} is ready! (Simulated)`);
                
                // Save to history
                saveToHistory(url, currentPlatform, currentFormat);
                
                // Reset button
                this.textContent = 'Convert';
                this.disabled = false;
            }, 2000);
        });
        
        // Initial render
        renderHistory();
        
        // Set default selected states
        document.querySelector('.youtube-btn').click();
        document.getElementById('mp4-btn').click();
        
        // Initially collapse history if empty
        if (conversionHistory.length === 0) {
            historyToggle.click();
        }
    </script>
</body>
</html>