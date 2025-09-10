# ERP Application Deployment Guide

## Overview
This document provides instructions for deploying the PA Energy ERP application to production using GitLab CI/CD.

## Prerequisites

### 1. GitLab Repository Setup
- ✅ Repository is already connected to: `https://gitlab.com/pa-energy/erp.git`
- ✅ Main branch is configured for automatic deployment

### 2. Required GitLab CI/CD Variables
You need to configure the following variables in GitLab:

**Path:** Project Settings > CI/CD > Variables

| Variable Name | Description | Type | Required |
|---------------|-------------|------|---------|
| `FTP_USER` | FTP username for deployment server | Variable | ✅ |
| `FTP_PASSWORD` | FTP password for deployment server | Variable (Masked) | ✅ |

### 3. Server Requirements
- FTP server: `ftp.paenergy.ph`
- Deployment path: `public_html/erp/`
- PHP 7.4+ with required extensions
- MySQL/MariaDB database

## Deployment Process

### Automatic Deployment
Deployment is triggered automatically when:
1. Code is pushed to the `main` branch
2. GitLab CI/CD pipeline runs with two stages:
   - **Build Stage**: Installs Node.js dependencies
   - **Deploy Stage**: Uploads changed files via FTP

### Manual Deployment
To trigger a manual deployment:
1. Go to GitLab project > CI/CD > Pipelines
2. Click "Run Pipeline"
3. Select `main` branch
4. Click "Run Pipeline"

## Pipeline Stages

### 1. Build Stage (`build_assets`)
- Installs Node.js dependencies using `npm ci`
- Caches `node_modules/` for faster subsequent builds
- Creates artifacts for the deployment stage

### 2. Deploy Stage (`deploy_production`)
- Downloads build artifacts
- Identifies changed files since last commit
- Uploads only modified files to reduce deployment time
- Provides detailed logging and error reporting

## Configuration Files

### `.gitlab-ci.yml`
The main CI/CD configuration file with:
- Build and deployment stages
- FTP upload with error handling
- Comprehensive logging
- Automatic cleanup

### `package.json`
Node.js dependencies:
- jQuery 3.7.1
- jsdom 25.0.1

## Troubleshooting

### Common Issues

#### 1. FTP Connection Failed
**Error:** `Failed to upload: [filename]`

**Solutions:**
- Verify FTP credentials in GitLab CI/CD variables
- Check if FTP server is accessible
- Ensure `FTP_USER` and `FTP_PASSWORD` are correctly set

#### 2. No Files to Deploy
**Message:** `No files changed in this commit. Skipping deployment.`

**Explanation:** This is normal when no files were modified in the commit.

#### 3. Build Stage Fails
**Error:** `npm ci` fails

**Solutions:**
- Check `package.json` for syntax errors
- Verify Node.js version compatibility
- Clear GitLab CI/CD cache if needed

#### 4. Permission Denied
**Error:** FTP upload permission errors

**Solutions:**
- Verify FTP user has write permissions
- Check directory permissions on server
- Ensure deployment path exists: `public_html/erp/`

### Monitoring Deployment

1. **GitLab Pipeline View:**
   - Go to Project > CI/CD > Pipelines
   - Click on pipeline ID to view details
   - Check job logs for detailed information

2. **Deployment Logs:**
   - Build stage shows dependency installation
   - Deploy stage shows file upload progress
   - Summary shows success/failure counts

### Manual Verification

After deployment, verify:
1. Application loads: `https://paenergy.ph/erp/`
2. Recent changes are reflected
3. No PHP errors in browser console
4. Database connectivity works

## Security Considerations

- FTP credentials are stored as masked variables in GitLab
- Only `main` branch triggers production deployment
- `.gitignore` excludes sensitive files from deployment
- FTP connection uses standard security settings

## Rollback Procedure

If deployment issues occur:
1. Identify the last working commit
2. Create a new commit reverting problematic changes
3. Push to `main` branch to trigger automatic redeployment

Alternatively:
1. Go to GitLab > Repository > Commits
2. Find the last working commit
3. Click "Revert" to create a revert commit
4. Merge the revert to trigger deployment

## Performance Optimization

- Only changed files are uploaded (incremental deployment)
- Node.js dependencies are cached between builds
- Build artifacts expire after 1 hour to save storage
- FTP timeouts and retries are configured for reliability

## Support

For deployment issues:
1. Check GitLab pipeline logs first
2. Verify server accessibility and permissions
3. Review this documentation for common solutions
4. Contact system administrator if issues persist

---

**Last Updated:** $(date)
**Pipeline Configuration:** `.gitlab-ci.yml`
**Application URL:** https://paenergy.ph/erp/