# Common Problems: Candidate Images Not Appearing on Home Page

## 🔍 **Most Common Issues & Solutions**

### **Problem 1: Web Server Cannot Access Upload Directory**
**Symptoms:** Images exist in `/uploads/` folder but show as broken links
**Cause:** Web server permissions or configuration issues

**Solution:**
```bash
# Check if XAMPP is running
# Ensure Apache service is started
# Verify uploads directory permissions
```

### **Problem 2: Incorrect File Paths in Database**
**Symptoms:** Database has image filenames but they don't match actual files
**Cause:** File upload process saves different filename than what's in database

**Check:** Run `/debug_image_issue.php` to compare DB vs actual files

### **Problem 3: Missing or Corrupted Image Files**
**Symptoms:** Database has filenames but files don't exist or are corrupted
**Cause:** Files were deleted, moved, or upload failed

**Solution:** Re-upload candidate images through admin panel

### **Problem 4: Path Resolution Issues**
**Symptoms:** Images work in admin but not on public home page
**Cause:** Different path contexts between admin and public pages

**Current Fix:** Using `getPublicCandidateImageUrl()` function

### **Problem 5: Browser Caching Issues**
**Symptoms:** Old images show or no images show after changes
**Cause:** Browser cached old versions

**Solution:** Hard refresh (Ctrl+F5) or clear browser cache

### **Problem 6: Database Query Issues**
**Symptoms:** No candidates appear on home page at all
**Cause:** SQL query fails or returns no results

**Check:** Verify candidates exist and have proper election associations

## 🧪 **Diagnostic Steps**

### **Step 1: Quick Visual Check**
Visit: `http://localhost/EMS2/test_direct_access.html`
- Tests direct image access via web URLs
- Shows immediate results with color coding

### **Step 2: Detailed Analysis**
Visit: `http://localhost/EMS2/simple_image_test.php`
- Comprehensive problem identification
- Tests database, files, and functions

### **Step 3: Debug Information**
Visit: `http://localhost/EMS2/debug_image_issue.php`
- Shows database vs file system comparison
- Tests image utility functions

### **Step 4: Home Page Console**
1. Visit: `http://localhost/EMS2/public/index.php`
2. Open browser Developer Tools (F12)
3. Check Console tab for image loading messages

## 🔧 **Quick Fixes**

### **Fix 1: Restart XAMPP**
```bash
# Stop and restart Apache in XAMPP Control Panel
# This fixes most web server access issues
```

### **Fix 2: Clear Browser Cache**
```bash
# Press Ctrl+F5 on home page
# Or clear browser cache completely
```

### **Fix 3: Check File Permissions**
```bash
# Ensure uploads directory is readable by web server
# Check that image files are not corrupted
```

### **Fix 4: Re-upload Images**
```bash
# Go to admin panel
# Re-upload candidate images
# Verify they appear in uploads directory
```

## 🎯 **Expected Behavior**

### **When Working Correctly:**
1. **Home page loads** with candidate cards
2. **Images display** for candidates with photos
3. **EMS logo shows** for candidates without photos
4. **Console shows** "Image loaded successfully" messages
5. **Hover tooltips** show actual image filenames

### **When Not Working:**
1. **Broken image icons** appear
2. **All images show EMS logo** (fallback)
3. **Console shows** "Image failed to load" errors
4. **Red borders** appear around failed images (if debug CSS active)

## 🚨 **Emergency Troubleshooting**

If images still don't work after trying above solutions:

1. **Check XAMPP Status:**
   - Ensure Apache is running (green in XAMPP Control Panel)
   - Check if `http://localhost/` works

2. **Verify File Structure:**
   ```
   C:\xampp\htdocs\EMS2\
   ├── uploads\
   │   ├── farmajo.jpeg
   │   ├── candidate_68a7723bc8dcd.jpeg
   │   └── (other image files)
   ├── assets\
   │   └── ems_intro.svg
   └── public\
       └── index.php
   ```

3. **Test Direct Access:**
   - Visit: `http://localhost/EMS2/uploads/farmajo.jpeg`
   - Should show the image directly
   - If 404 error, web server can't access uploads

4. **Check Database:**
   - Ensure candidates table has data
   - Verify profile_image column has filenames
   - Check if filenames match actual files

## 📞 **Getting Help**

Run these diagnostic tools and share results:
1. `/simple_image_test.php` - Shows all problems at once
2. `/debug_image_issue.php` - Detailed technical information
3. Browser console output from home page
4. XAMPP error logs (if any)

This will help identify the exact cause of the image display problems.