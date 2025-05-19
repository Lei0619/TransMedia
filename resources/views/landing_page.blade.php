<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Converter</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        
        .converter-container {
            background-color: #e0e0e0;
            width: 300px;
            padding: 30px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .file-circle {
            width: 120px;
            height: 120px;
            background-color: #6e6e6e;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        
        .file-input-container {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .file-input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 25px;
            background-color: white;
            outline: none;
            box-sizing: border-box;
        }
        
        .convert-button {
            background-color: #0066ff;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 0;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .convert-button:hover {
            background-color: #0052cc;
        }
        
        .file-label {
            cursor: pointer;
            width: 100%;
        }
        
        #file-name {
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        
        #actual-file {
            display: none;
        }
    </style>
</head>
<body>
    <div class="converter-container">
        <div class="file-circle" id="file-preview"></div>
        
        <div class="file-input-container">
            <label for="actual-file" class="file-label">
                <input type="text" class="file-input" id="file-name" readonly placeholder="Select a file">
                <input type="file" id="actual-file" accept=".mp3,.mp4">
            </label>
        </div>
        
        <button class="convert-button" id="convert-btn">Convert now</button>
    </div>

    <script>
        document.getElementById('actual-file').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'No file selected';
            document.getElementById('file-name').value = fileName;
            
            // prev
        //     const file = e.target.files[0];
        //     if (file && file.type.includes('video')) {
        //         const filePreview = document.getElementById('file-preview');
        //         const videoPreview = document.createElement('video');
                
        //         videoPreview.src = URL.createObjectURL(file);
        //         videoPreview.style.width = '100%';
        //         videoPreview.style.height = '100%';
        //         videoPreview.style.borderRadius = '50%';
        //         videoPreview.style.objectFit = 'cover';
                
        //         filePreview.innerHTML = '';
        //         filePreview.appendChild(videoPreview);
        //     }
        // });
        
        document.getElementById('convert-btn').addEventListener('click', function() {
            const file = document.getElementById('actual-file').files[0];
            if (!file) {
                alert('Please select a file first');
                return;
            }
            
            //send the file to your server for conversion
            const convertButton = document.getElementById('convert-btn');
            convertButton.textContent = 'Converting...';
            convertButton.disabled = true;
            
            setTimeout(function() {
                alert('Conversion complete! (Simulated)');
                convertButton.textContent = 'Convert now';
                convertButton.disabled = false;
            }, 2000);
        });
        
        // Make the whole input container clickable to open file dialog
        document.querySelector('.file-input-container').addEventListener('click', function() {
            document.getElementById('actual-file').click();
        });
    </script>
</body>
</html>