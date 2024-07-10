# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.6] - 2024-07-10

### Changed

- Restored toggle code to active. 
- Removed additional debug code. 

## [1.1.5] - 2024-07-10

### Added

- Moved all custom css out of the customizer and into the assets/css/custom.css file
- Added automated version constant define based on style.css setting. Eliminaes need to set twice. 

## [1.1.4] - 2024-07-09

### Changed

- Revert to original key names. 
- Added function to handle email notifications. 
- Added debuging code to turn off use of email toggle notification. Requires WP_DEBUG to be true. 

## [1.1.3] - 2024-07-04

### Added

- Google Tag Manager code to functions.php
- removed commented out code in navigation_functions.php

### Fixed

- Corrected index typo in save_referral_data.php

## [1.1.2] - 2024-06-25

### Fixed

- Adjusted $wpdb->prepare statement for SQL select where IN used. 
- Removed numerous debug statments. 
- Consolidated initialization statments. 

## [1.1.1] - 2024-06-14

### Changed

- Adjusted variables used to determine receiving / sending chapters. 
- Fixed logic to remove redundant entries
- Changed output on referrals table to use sending name or receiving name as required. 
- Removed debug code. 

## [1.1.0] - 2024-06-13

### Added

 - Replace Relationship with Networking throughout
 - Changes for referral type term change from 'relationships' to 'networking'
 - Functionality to generate active chapter list for referral selection
 - Funciton to only permit filtering by chapter presidents. New navigation. 
 - Logic to display list of active chapters that can be filtered. User's chapter is default. 
 - Filtering logic to javascript. 
 - New functions get_active_chapter_list, is_chapter_president, userids_in_chapter.
 - This CHANGELOG.md file. 

### Changed

 - New role chapter_president to replace individual named chapter roles. 


### Fixed

- n/a

### Removed

- - Limits to only display results for Lititz chapter.