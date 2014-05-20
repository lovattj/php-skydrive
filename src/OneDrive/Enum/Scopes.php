<?php
namespace OneDrive\Enum;

class Scopes {

    //<editor-fold desc='Core'>

    /**
     * Read access to a user's basic profile info. Also enables read access to a user's list of contacts.
     */
    const BASIC = 'wl.basic';

    /**
     * The ability of an app to read and update a user's info at any time.
     * Without this scope, an app can access the user's info only while the user is signed in to their Microsoft account and is using your app.
     */
    const OFFLINE_ACCESS = 'wl.offline_access';

    /**
     * Single sign-in behavior. With single sign-in, users who are already signed in to their Microsoft account are also signed in to your website.
     */
    const SIGNIN = 'wl.signin';
    //</editor-fold desc='Core'>

    //<editor-fold desc='Etended'>

    /**
     * Read access to a user's birthday info including birth day, month, and year.
     */
    const BIRTHDAY = 'wl.birthday';

    /**
     * Read access to a user's calendars and events.
     */
    const CALENDARS = 'wl.calendars';

    /**
     * Read and write access to a user's calendars and events.
     */
    const CALENDARS_UPDATE = 'wl.calendars_update';

    /**
     * Read access to the birth day and birth month of a user's contacts.
     * Note that this also gives read access to the user's birth day, birth month, and birth year.
     */
    const CONTACTS_BIRTHDAY ='wl.contacts_birthday';

    /**
     * Creation of new contacts in the user's address book.
     */
    const CONTACTS_CREATE = 'wl.contacts_create';

    /**
     * Read access to a user's calendars and events.
     * Also enables read access to any calendars and events that other users have shared with the user.
     */
    const CONTACTS_CALENDARS = 'wl.contacts_calendars';

    /**
     * Read access to a user's albums, photos, videos, and audio, and their associated comments and tags.
     * Also enables read access to any albums, photos, videos, and audio that other users have shared with the user.
     */
    const CONTACTS_PHOTOS = 'wl.contacts_photos';

    /**
     * Read access to Microsoft OneDrive files that other users have shared with the user.
     * Note that this also gives read access to the user's files stored in OneDrive.
     */
    const CONTACTS_SKYDRIVE = 'wl.contacts_skydrive';

    /**
     * Read access to a user's personal, preferred, and business email addresses.
     */
    const EMAILS = 'wl.emails';

    /**
     * Creation of events on the user's default calendar.
     */
    const EVENTS_CREATE ='wl.events_create';

    /**
     * Read and write access to a user's email using IMAP, and send access using SMTP.
     */
    const IMAP = 'wl.imap';

    /**
     * Read access to a user's personal, business, and mobile phone numbers.
     */
    const PHONE_NUMBERS = 'wl.phone_numbers';

    /**
     * Read access to a user's photos, videos, audio, and albums.
     */
    const PHOTOS = 'wl.photos';

    /**
     * Read access to a user's postal addresses.
     */
    const POSTAL_ADDRESSES = 'wl.postal_addresses';

    /**
     * Read access to a user's files stored in OneDrive.
     */
    const SKYDRIVE = 'wl.skydrive';

    /**
     * Read and write access to a user's files stored in OneDrive.
     */
    const SKYDRIVE_UPDATE = 'wl.skydrive_update';

    /**
     * Read access to a user's employer and work position information.
     */
    const WORK_PROFILE = 'wl.work_profile';

    /**
     * Read and write access to a user's OneNote notebooks stored in OneDrive.
     */
    const ONENOTE_CREATE = 'office.onenote_create';

    //</editor-fold desc='Etended'>
} 