# PipraPay Tool APK Download Guide

## 📱 Overview

The PipraPay Tool APK download system provides users with access to both old and new versions of the Android application directly from the admin panel. This guide explains how the download mechanism works and how to manage different APK versions.

> **📝 Current Status (29 October 2025)**: All APK versions are currently identical. In the future, we will update the stable (Old) and new (Beta) versions to provide different functionality and features.

## 🏗️ System Architecture

### File Structure
```
docs/apps/
├── piprapay-tool.apk         # Fallback/original APK file
├── piprapay-tool-old.apk     # Old version APK (stable)
└── piprapay-tool-new.apk     # New version APK (latest)
```

### Download Flow
```
User Click → JavaScript → Server Request → APK File → Download
```

## 🔧 How It Works

### 1. Frontend Interface

**Location**: Admin Panel → SMS Data Section

**Download Buttons**:
- **"Download Old Version App"** - Downloads stable version
- **"Download New Version App"** - Downloads latest version

**Files Involved**:
- `pp-include/pp-resource/sms-data.php`
- `pp-include/pp-resource/sms-data-devices.php`

### 2. JavaScript Implementation

```javascript
function triggerDownload(fileName) {
    var link = document.createElement('a');
    link.href = 'https://<?php echo $_SERVER['HTTP_HOST']?>/admin/sms-data?download=' + encodeURIComponent(fileName);
    link.download = 'piprapay-tool-' + fileName + '.apk';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
```

**What happens**:
1. Creates a hidden download link
2. Sets the URL with download parameter (`old` or `new`)
3. Sets descriptive filename for download
4. Programmatically clicks the link to start download

### 3. Server-Side Processing

**File**: `admin/index.php`

**Logic Flow**:
```php
if(isset($_GET['download'])){
    $download_type = $_GET['download']; // 'old' or 'new'
    
    // Define version-specific APK paths
    $apk_files = [
        'old' => dirname(__DIR__) . '/docs/apps/piprapay-tool-old.apk',
        'new' => dirname(__DIR__) . '/docs/apps/piprapay-tool-new.apk'
    ];
    
    // Fallback to single APK if version-specific doesn't exist
    $fallback_apk = dirname(__DIR__) . '/docs/apps/piprapay-tool.apk';
    
    // Serve the appropriate APK file
}
```

## 📋 Download Process Details

### Step-by-Step Process

1. **User Action**: Clicks "Download Old Version App" or "Download New Version App"

2. **JavaScript Execution**: 
   - `triggerDownload('old')` or `triggerDownload('new')` is called
   - Creates download URL: `/admin/sms-data?download=old` or `/admin/sms-data?download=new`

3. **Server Processing**:
   - Checks for version-specific APK file first
   - Falls back to generic APK if version-specific doesn't exist
   - Sets proper HTTP headers for APK download

4. **File Delivery**:
   - Sets MIME type: `application/vnd.android.package-archive`
   - Sets filename: `piprapay-tool-old.apk` or `piprapay-tool-new.apk`
   - Streams file content to browser

5. **Download Completion**: User receives the APK file ready for installation

## 🎯 Version Management

### Priority System

1. **First Priority**: Version-specific APK files
   - `piprapay-tool-old.apk` for old version
   - `piprapay-tool-new.apk` for new version

2. **Fallback**: Generic APK file
   - `piprapay-tool.apk` used if version-specific files don't exist

3. **Last Resort**: GitHub redirect
   - Redirects to `https://github.com/PipraPay/PipraPay-Open-Source-App/`

### File Management

**To add new versions**:
1. Upload new APK to `docs/apps/` directory
2. Name it `piprapay-tool-new.apk` for latest version
3. Rename current new version to `piprapay-tool-old.apk` for stable version

**File naming convention**:
- Downloaded files: `piprapay-tool-old.apk`, `piprapay-tool-new.apk`
- Server files: Same naming convention in `docs/apps/`

## 🔒 Security Features

### HTTP Headers
```php
header('Content-Type: application/vnd.android.package-archive');
header('Content-Disposition: attachment; filename="piprapay-tool-old.apk"');
header('Content-Length: ' . filesize($apk_file_path));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
```

### File Validation
- Checks file existence before serving
- Validates file paths to prevent directory traversal
- Proper error handling with fallback mechanisms

## 🚀 Benefits

### For Users
- **Choice**: Can select between stable (old) and latest (new) versions
- **Reliability**: Fallback system ensures download always works
- **Convenience**: Direct download from admin panel

### For Developers
- **Flexibility**: Easy version management
- **Backward Compatibility**: Works with existing single APK setup
- **Scalability**: Can easily add more version types

## 🛠️ Troubleshooting

### Common Issues

**Download not working**:
1. Check if APK files exist in `docs/apps/` directory
2. Verify file permissions (should be readable)
3. Check server PHP configuration for file downloads

**Wrong file downloaded**:
1. Verify APK file naming convention
2. Check JavaScript function parameters
3. Ensure server-side logic matches frontend calls

**Redirect to GitHub instead of download**:
1. APK files are missing from `docs/apps/` directory
2. File permissions issue
3. Server configuration problem

## 📝 Implementation Notes

### Backward Compatibility
- System works with existing single APK file setup
- No breaking changes to current functionality
- Graceful degradation if version-specific files are missing