CHANGELOG
=========

**1.2.0**:

* Fix: Moved default `cgmconfigadmin` route out of module.config.php and into
  cgmconfigadmin.global.php.dist to allow for easier route customization.
  In the case where the `cgmconfigadmin` route may be needed as a child route elsewhere,
  removing routes via overridden configurations is (currently) not straightforward:
  https://github.com/zendframework/zf2/issues/3823

**1.1.3**:

* Feature: Added Textarea input type (@Celtico)
* Feature: Added input types: Color, Date, DateTimeLocal, Email, Month
  Range, Time, Url, and Week.
* Fix: Default filters and validators are now being added from
  element's getInputSpecification(), if it exists.

**1.1.2**:

* Feature: Added Password input type (@Celtico)
* Fix: Update for ZfcBase AbstractDbMapper changes (@Celtico)

**1.1.1**:

* Feature: Ability to disable preview mode for a config context.
* Fix: Autoload classmap updated for 1.1.0.

**1.1.0**:

* Feature: Per-user configuration settings, causing API changes to the config format.
* Feature: Configurations can now be grouped into any type of context
  (ie. site-wide, per user, per group, etc.), not just per-user.
  See example in Module.php.
* Feature: Simplified schema so that key-value stores or transactionless
  databases can be used. (Schema changes)

**1.0.0**:

* First release, site-wide configuration settings.
* Feature: Settings can be easily configured for a particular Form input type
  (Radio, Select, MultiCheckbox, Text, Range, etc.).
* Feature: Preview settings in the administrator's browser before publishing.
* Feature: Twitter Bootstrap v2 UI classes.
* Feature: Multiple rendering options for the settings form. Two view helpers
  included (Fieldsets and Accordian).

