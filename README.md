# Adobe Certified Professional Developer Training Project

The code in this project is meant to be used with the SwiftOtter training course for Adobe Certified Professional -
Adobe Commerce Developer AD0-E711.

## Compatibility

This project has been specifically tested on **Magento Open Source 2.4.3**.

## Installation

This project is meant to be used with an _existing_ Magento installation.

In your Magento root directory, create a `training-modules` directory and clone this project into
`training-modules/professional-developer-project`:

```
git clone git@github.com:swiftotter-certifications/project-professional-developer.git training-modules/project-professional-developer
```

Then set up a local Composer repository:

```
composer config repositories.training-pro-dev '{"type": "path", "url": "training-modules/project-professional-developer/src/*", "options": {"symlink": true}}'
```

## Example modules

Packages in `src` starting with `example-` are not meant to be directly installed but serve as example code for the
exercises in the course, or else are intended to be copied into `app/code` as directed by the course instructions.

## Prerequisites

Some packages in `src` are necessary prerequisites to the coding exercises in the course and should be installed from the
local Composer repository when indicated in the course instructions.
