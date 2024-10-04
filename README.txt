# WP External Guard

![Plugin Banner](https://github.com/amarasa/wp-external-guard/blob/main/assets/images/banner.png?raw=true)

WP External Guard is a versatile WordPress plugin that enhances user experience by displaying stylish SweetAlert notifications when visitors click on external links. This proactive feature informs users before they navigate away from your site, fostering transparency and trust. With customizable settings, including excluded domains and button colors, WP External Guard seamlessly integrates into any website design.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
  - [Configuration](#configuration)
  - [Excluded Domains](#excluded-domains)
  - [Customizing Alert Messages and Titles](#customizing-alert-messages-and-titles)
  - [Customizing Button Colors](#customizing-button-colors)
- [Screenshots](#screenshots)
- [Frequently Asked Questions (FAQ)](#frequently-asked-questions-faq)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

## Features

- **External Link Detection:** Automatically detects and prompts users when they click on external links.
- **SweetAlert Integration:** Utilizes SweetAlert2 for elegant and responsive alert dialogs.
- **Customizable Settings:**
  - **Excluded Domains:** Specify domains that should bypass the alert prompt.
  - **Alert Messages and Titles:** Personalize the alert's content to match your site's tone.
  - **Button Texts and Colors:** Customize the "Confirm" and "Cancel" button texts and colors for better visual integration.
- **User-Friendly Settings Page:** Easy-to-use interface within the WordPress admin dashboard.
- **Automatic Updates:** Stay up-to-date with the latest features and security patches via GitHub.
- **Translation-Ready:** Fully compatible with WordPress translation functions for multilingual support.
- **Lightweight and Efficient:** Minimal impact on site performance.

## Installation

1. **Download the Plugin:**
   - Clone the repository or download the ZIP file from [GitHub](https://github.com/amarasa/wp-external-guard).

2. **Upload to WordPress:**
   - Navigate to your WordPress admin dashboard.
   - Go to **Plugins > Add New > Upload Plugin**.
   - Click **Choose File**, select the downloaded `wp-external-guard.zip` file, and click **Install Now**.

3. **Activate the Plugin:**
   - After installation, click the **Activate Plugin** button.

## Usage

Once activated, WP External Guard automatically starts monitoring external links on your website. To customize settings:

### Configuration

1. **Access Plugin Settings:**
   - From the WordPress admin dashboard, navigate to **Settings > WP External Guard**.

2. **Excluded Domains:**
   - **Purpose:** Prevent the alert from appearing on specific domains (e.g., your partner sites or essential external resources).
   - **How to Add:**
     - Enter one domain per line in the **Excluded Domains** textarea.
     - **Format:** Do not include `http://` or `https://`.
     - **Example:**
       ```
       google.com
       another-excluded-domain.com
       subdomain.example.com
       ```
   - **Note:** The plugin automatically normalizes the input, removing protocols and `www.` prefixes.

3. **Customizing Alert Messages and Titles:**
   - **Alert Message:**
     - **Field:** `Alert Message`
     - **Description:** The message displayed in the alert dialog.
     - **Default:** "You are about to leave our site. Continue?"
     - **Usage:** Enter a concise and clear message to inform users about navigating away.
   - **Alert Title:**
     - **Field:** `Alert Title`
     - **Description:** The title of the alert dialog.
     - **Default:** "External Link"
     - **Usage:** Enter a descriptive title that aligns with the alert message.

4. **Customizing Button Colors:**
   - **Confirm Button Color:**
     - **Field:** `Confirm Button Color`
     - **Description:** Select a color for the "Yes, proceed" button.
     - **Default:** `#3085d6` (SweetAlert2 Blue)
   - **Cancel Button Color:**
     - **Field:** `Cancel Button Color`
     - **Description:** Select a color for the "Cancel" button.
     - **Default:** `#d33` (SweetAlert2 Red)
   - **Usage:** Use the color pickers to choose colors that match your site's design. Ensure sufficient contrast for readability.

5. **Customizing Button Texts:**
   - **Confirm Button Text:**
     - **Field:** `Confirm Button Text`
     - **Description:** Text displayed on the confirmation button.
     - **Default:** "Yes, proceed"
   - **Cancel Button Text:**
     - **Field:** `Cancel Button Text`
     - **Description:** Text displayed on the cancel button.
     - **Default:** "Cancel"

6. **Save Settings:**
   - After configuring all settings, click the **Save Changes** button to apply your customizations.

### Excluded Domains

- **Purpose:** To exempt certain external domains from triggering the alert dialog.
- **How to Use:**
  - Navigate to **Settings > WP External Guard**.
  - In the **Excluded Domains** textarea, enter one domain per line.
  - **Example:**
    ```
    google.com
    another-excluded-domain.com
    ```
  - **Note:** Do not include protocols (`http://`, `https://`) or trailing slashes (`/`).

### Customizing Alert Messages and Titles

- **Alert Message:**
  - Allows for detailed or concise messages.
  - Supports multi-line text via the textarea input.
- **Alert Title:**
  - Provides context for the alert.
  - Should be succinct and clear.

### Customizing Button Colors

- **Confirm Button:**
  - Choose a color that signifies positive action.
  - Default is a neutral blue, suitable for most designs.
- **Cancel Button:**
  - Choose a color that signifies caution or negative action.
  - Default is a red shade, aligning with standard alert practices.

## Screenshots

### 1. **Settings Page**

![Settings Page](https://github.com/amarasa/wp-external-guard/blob/main/assets/images/settings-page.png?raw=true)

*Configure excluded domains, alert messages, titles, and button colors.*

### 2. **SweetAlert Confirmation Dialog**

![SweetAlert Dialog](https://github.com/amarasa/wp-external-guard/blob/main/assets/images/sweetalert-dialog.png?raw=true)

*Customized alert dialog appearing upon clicking an external link.*

## Frequently Asked Questions (FAQ)

### 1. **How does WP External Guard determine if a link is external?**

WP External Guard checks the hostname of each link. If the hostname differs from your site's domain and is not listed in the excluded domains, it is considered external, triggering the SweetAlert confirmation dialog.

### 2. **Can I exclude subdomains from triggering the alert?**

Yes! When you exclude a domain, all its subdomains are automatically excluded. For example, excluding `example.com` will also exclude `sub.example.com`.

### 3. **Why is the alert still appearing for an excluded domain?**

Ensure that you have entered only the domain name without protocols (`http://`, `https://`) and trailing slashes (`/`). For example, use `google.com` instead of `https://google.com/`.

### 4. **Can I customize the button texts further?**

Currently, you can customize the texts for the confirm and cancel buttons via the settings page. For more advanced customizations, consider modifying the plugin's JavaScript or extending its functionality.

### 5. **Is WP External Guard compatible with all WordPress themes?**

Yes, WP External Guard is designed to be theme-agnostic. However, always test the plugin with your specific theme to ensure compatibility, especially concerning button colors and alert styles.

## Changelog

### 1.0.1

- **Added:**
  - Color picker fields for Confirm and Cancel buttons in the settings page.
  - Sanitization for color inputs to ensure valid hex codes.
- **Updated:**
  - `elas_message_render` function to use `<textarea>` for multi-line alert messages.
  - JavaScript to apply user-selected button colors in SweetAlert configuration.
- **Fixed:**
  - Normalization of excluded domains to remove protocols and `www.` prefixes.

### 1.0.0

- **Initial Release:**
  - External link detection with SweetAlert confirmation.
  - Settings page for customizing excluded domains, alert messages, titles, and button texts.

## Contributing

Contributions are welcome! Whether you're fixing bugs, improving documentation, or suggesting new features, your input helps make WP External Guard better.

### How to Contribute

1. **Fork the Repository:**
   - Click the **Fork** button at the top right of the repository page on GitHub.

2. **Clone Your Fork:**
   ```bash
   git clone https://github.com/your-username/wp-external-guard.git
