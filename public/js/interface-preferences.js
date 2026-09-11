(() => {
    'use strict';

    const storageKeys = {
        language: 'gaspoa_language',
        theme: 'gaspoa_theme',
    };

    // These cover navigation, authentication and the actions repeated across dashboards.
    // New Blade screens can opt in explicitly with data-i18n without changing this script.
    const translations = {
        en: {
            'Lugha': 'Language',
            'Kiswahili': 'Swahili',
            'Kiingereza': 'English',
            'Ingia': 'Sign in',
            'Jisajili': 'Register',
            'Toka': 'Sign out',
            'Nyumbani': 'Home',
            'Dashboard': 'Dashboard',
            'Akaunti': 'Account',
            'Profaili': 'Profile',
            'Mipangilio': 'Settings',
            'Mipangilio ya haraka': 'Quick settings',
            'Mipangilio ya Akaunti': 'Account settings',
            'Watumiaji': 'Users',
            'Bidhaa': 'Products',
            'Hisa': 'Inventory',
            'Historia': 'History',
            'Arifa': 'Notifications',
            'Agiza Sasa': 'Order now',
            'Agiza Gesi Jumla': 'Order wholesale gas',
            'Fuatilia': 'Track',
            'Fuatilia Agizo': 'Track order',
            'Maagizo Mapya': 'New orders',
            'Maagizo Wateja': 'Customer orders',
            'Maagizo ya Jumla': 'Wholesale orders',
            'Duka Langu': 'My shop',
            'Ghala Langu': 'My warehouse',
            'Ripoti': 'Reports',
            'Ripoti za Fedha': 'Financial reports',
            'Bei': 'Pricing',
            'Bei & Uchumi': 'Pricing & finance',
            'Kumbukumbu (Logs)': 'Activity logs',
            'Haki zote zimehifadhiwa.': 'All rights reserved.',
            'Huduma:': 'Service:',
            'Karibu Tena': 'Welcome back',
            'Dark mode': 'Dark mode',
            'Light mode': 'Light mode',
            'Switch to dark mode': 'Switch to dark mode',
            'Switch to light mode': 'Switch to light mode',
            'Tafadhali ingia ili kuendelea.': 'Please sign in to continue.',
            'Barua pepe': 'Email address',
            'Nenosiri': 'Password',
            'Thibitisha Nenosiri': 'Confirm password',
            'Jina Kamili': 'Full name',
            'Namba ya Simu': 'Phone number',
            'Umesahau nenosiri?': 'Forgot password?',
            'Umesahau Nywila?': 'Forgot password?',
            'Tayari una akaunti?': 'Already have an account?',
            'Huna akaunti?': 'Do not have an account?',
            'Ingia Hapa': 'Sign in here',
            'Jisajili Bure': 'Register for free',
            'Rudi Nyumbani': 'Back to home',
            'Ingia kwenye Akaunti Yako': 'Sign in to your account',
            'Fungua Akaunti Mpya': 'Create a new account',
            'Barua Pepe au Namba ya Simu': 'Email address or phone number',
            'Nywila': 'Password',
            'Thibitisha Nywila': 'Confirm password',
            'Nikumbuke': 'Remember me',
            'Ungependa Kujisajili Kama Nani?': 'How would you like to register?',
            '-- Chagua --': '-- Select --',
            'Mtumiaji wa Kawaida (Nahitaji Gesi)': 'Customer (I need gas)',
            'Mfanyabiashara Rejareja (Duka la Gesi)': 'Retailer (gas shop)',
            'Muuza Jumla (Ghala)': 'Wholesaler (warehouse)',
            'Nakubali': 'I accept',
            'Masharti na Vigezo': 'Terms and conditions',
            'Jisajili Sasa': 'Register now',
            'Jinsi Inavyofanya Kazi': 'How it works',
            'Unahitaji Huduma Gani Leo?': 'What service do you need today?',
            'Chagua moja ya huduma zetu hapa chini': 'Choose one of our services below',
            'Kubadilisha Mtungi (Refill)': 'Cylinder exchange (refill)',
            'Kununua Mtungi Mpya': 'Buy a new cylinder',
            'Chagua Refill': 'Choose refill',
            'Chagua Mtungi Mpya': 'Choose new cylinder',
            'Huna Simu Janja au Intaneti?': 'No smartphone or internet?',
            'Haraka': 'Fast',
            'Rahisi': 'Easy',
            'Salama': 'Safe',
            'Jinsi GasPOA Inavyofanya Kazi': 'How GasPOA works',
            'Hatua tatu rahisi kupata gesi yako': 'Three simple steps to get your gas',
            'Chagua Huduma': 'Choose a service',
            'Tunakutafutia Muuzaji': 'We find a seller',
            'Pokea na Lipa': 'Receive and pay',
            'Unamiliki Duka la Gesi au Ghala?': 'Do you own a gas shop or warehouse?',
            'Jisajili Kama Mfanyabiashara': 'Register as a business',
            'Unahitaji Gesi Sasa?': 'Need gas now?',
            'Kubadilisha (Refill)': 'Exchange (refill)',
            'Mtungi Mpya': 'New cylinder',
            'Agizo Linaloendelea': 'Active order',
            'Njiani': 'On the way',
            'Imekubaliwa': 'Accepted',
            'Imeshachukuliwa': 'Picked up',
            'Inasubiri': 'Pending',
            'Jumla:': 'Total:',
            'Malipo yamekamilika.': 'Payment completed.',
            'Hifadhi': 'Save',
            'Ghairi': 'Cancel',
            'Tafuta': 'Search',
            'Hariri': 'Edit',
            'Futa': 'Delete',
            'Rudi': 'Back',
            'Endelea': 'Continue',
            'Tuma': 'Send',
            'Weka upya nenosiri': 'Reset password',
        },
        sw: {},
    };

    const translatableAttributes = ['placeholder', 'title', 'aria-label'];
    const originalText = new WeakMap();

    function getStoredPreference(key) {
        try {
            return localStorage.getItem(storageKeys[key]);
        } catch (error) {
            return null;
        }
    }

    function storePreference(key, value) {
        try {
            localStorage.setItem(storageKeys[key], value);
        } catch (error) {
            // Browser privacy settings can block localStorage; the cookie still preserves the choice.
        }

        document.cookie = `${storageKeys[key]}=${encodeURIComponent(value)}; path=/; max-age=31536000; SameSite=Lax`;
    }

    function translateValue(value, language) {
        if (language !== 'en' || !value) {
            return value;
        }

        return translations.en[value.trim()] || value;
    }

    function translateDocument(language) {
        document.querySelectorAll('[data-i18n]').forEach((element) => {
            const source = element.dataset.i18n;
            element.textContent = translateValue(source, language);
        });

        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
            acceptNode(node) {
                const parent = node.parentElement;
                if (!parent || ['SCRIPT', 'STYLE', 'TEXTAREA', 'OPTION'].includes(parent.tagName) || parent.closest('[data-no-translate]')) {
                    return NodeFilter.FILTER_REJECT;
                }

                return node.nodeValue.trim() ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
            },
        });

        const textNodes = [];
        while (walker.nextNode()) {
            textNodes.push(walker.currentNode);
        }

        textNodes.forEach((node) => {
            const source = originalText.get(node) || node.nodeValue;
            originalText.set(node, source);
            const translation = translateValue(source, language);
            if (translation !== node.nodeValue) {
                node.nodeValue = source.replace(source.trim(), translation);
            }
        });

        document.querySelectorAll('*').forEach((element) => {
            translatableAttributes.forEach((attribute) => {
                if (element.hasAttribute(attribute)) {
                    const sourceAttribute = `i18n${attribute.charAt(0).toUpperCase()}${attribute.slice(1)}`;
                    const source = element.dataset[sourceAttribute] || element.getAttribute(attribute);
                    element.dataset[sourceAttribute] = source;
                    element.setAttribute(attribute, translateValue(source, language));
                }
            });
        });
    }

    function applyPreferences(language, theme) {
        const safeLanguage = language === 'en' ? 'en' : 'sw';
        const safeTheme = theme === 'dark' ? 'dark' : 'light';
        const root = document.documentElement;

        root.lang = safeLanguage;
        root.dataset.theme = safeTheme;
        translateDocument(safeLanguage);

        document.querySelectorAll('[data-interface-preferences]').forEach((control) => {
            const languageSelect = control.querySelector('[data-language-select]');
            const themeLabel = control.querySelector('[data-theme-label]');
            const themeButton = control.querySelector('[data-theme-toggle]');
            const themeIcon = control.querySelector('[data-theme-icon]');
            const darkMode = safeTheme === 'dark';

            if (languageSelect) languageSelect.value = safeLanguage;
            if (themeLabel) themeLabel.textContent = darkMode
                ? (safeLanguage === 'en' ? 'Light mode' : 'Mwonekano wa mwanga')
                : (safeLanguage === 'en' ? 'Dark mode' : 'Mwonekano wa giza');
            if (themeButton) {
                if (themeButton instanceof HTMLInputElement && themeButton.type === 'checkbox') {
                    themeButton.checked = darkMode;
                }
                const label = darkMode
                    ? (safeLanguage === 'en' ? 'Switch to light mode' : 'Badili kwenda mwonekano wa mwanga')
                    : (safeLanguage === 'en' ? 'Switch to dark mode' : 'Badili kwenda mwonekano wa giza');
                themeButton.setAttribute('aria-label', label);
                themeButton.setAttribute('title', label);
            }
            if (themeIcon) themeIcon.className = darkMode ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
        });
    }

    function persistForSignedInUser(control, language, theme) {
        if (control.dataset.authenticated !== 'true' || !control.dataset.preferencesUrl) return;

        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch(control.dataset.preferencesUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...(token ? { 'X-CSRF-TOKEN': token } : {}),
            },
            credentials: 'same-origin',
            body: JSON.stringify({ interface_language: language, interface_theme: theme }),
        }).catch(() => {
            // Local preferences remain active even if the connection is temporarily unavailable.
        });
    }

    function initialisePreferences() {
        const controls = [...document.querySelectorAll('[data-interface-preferences]')];
        const firstControl = controls[0];
        const language = firstControl?.dataset.language || getStoredPreference('language') || 'sw';
        const theme = firstControl?.dataset.theme || getStoredPreference('theme') || 'light';

        applyPreferences(language, theme);

        // Carry a visitor's choice into their new account on the first signed-in page.
        if (firstControl?.dataset.authenticated === 'true'
            && (!firstControl.dataset.language || !firstControl.dataset.theme)) {
            persistForSignedInUser(firstControl, language, theme);
        }

        controls.forEach((control) => {
            control.querySelector('[data-language-select]')?.addEventListener('change', (event) => {
                const nextLanguage = event.target.value;
                const currentTheme = document.documentElement.dataset.theme || 'light';
                storePreference('language', nextLanguage);
                applyPreferences(nextLanguage, currentTheme);
                persistForSignedInUser(control, nextLanguage, currentTheme);
            });

            const themeToggle = control.querySelector('[data-theme-toggle]');
            const updateTheme = () => {
                const currentLanguage = document.documentElement.lang || 'sw';
                const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
                storePreference('theme', nextTheme);
                applyPreferences(currentLanguage, nextTheme);
                persistForSignedInUser(control, currentLanguage, nextTheme);
            };

            if (themeToggle instanceof HTMLInputElement && themeToggle.type === 'checkbox') {
                themeToggle.addEventListener('change', updateTheme);
            } else {
                themeToggle?.addEventListener('click', updateTheme);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialisePreferences, { once: true });
    } else {
        initialisePreferences();
    }
})();
