# pdWidget Bundle
Flexible widget system for Symfony 4+. 

[![Latest Stable Version](https://poser.pugx.org/rmznpydn/pd-widget/v/stable)](https://packagist.org/packages/rmznpydn/pd-widget)
[![Total Downloads](https://poser.pugx.org/rmznpydn/pd-widget/downloads)](https://packagist.org/packages/rmznpydn/pd-widget)
[![Latest Unstable Version](https://poser.pugx.org/rmznpydn/pd-widget/v/unstable)](https://packagist.org/packages/rmznpydn/pd-widget)
[![License](https://poser.pugx.org/rmznpydn/pd-widget/license)](https://packagist.org/packages/rmznpydn/pd-widget)

Installation
---

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require rmznpydn/pd-widget
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

With Symfony 4, the package will be activated automatically. But if something goes wrong, you can install it manually.

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
<?php
// config/bundles.php

return [
    //...
    Pd\WidgetBundle\PdWidgetBundle::class => ['all' => true]
];
```

Add Widget Routing:

```yaml
#config/routes.yaml

# Widget Routing
widget:
    resource: "@PdWidgetBundle/Resources/config/routing.yml"
```

Edit Doctrine Settings (`config/packages/doctrine.yaml`):

```yaml
doctrine:
    orm:
        resolve_target_entities:
            Pd\WidgetBundle\Entity\UserInterface: App\Entity\User
```

UserInterface field, enter the class for the existing authorization system.

### Step 3: Settings Bundle (Optional)
You can specify the template for the widget container.
```yaml
# config/packages/framework.yaml

pd_widget:
    base_template: '@PdWidget/widgetBase.html.twig'
```

Create Your First Widget
---

#### Step 1: Create Widget Event Listener

Widgets work with Event. Create Widget with Event Listener

```php
<?php
// src/Widgets/DashboardWidget.php

namespace App\Widgets;

use Pd\WidgetBundle\Builder\Item;
use Pd\WidgetBundle\Event\WidgetEvent;

class Dashboard
{
    public function builder(WidgetEvent $event)
    {
        // Get Widget Container
        $widgets = $event->getWidgetContainer();

        // Add Widgets
        $widgets
            ->addWidget((new Item('user_info'))
                ->setGroup('admin')
                ->setName('widget_user_info.name')
                ->setDescription('widget_user_info.description')
                ->setTemplate('widgets/userInfo.html.twig')
                //->setContent('pdWidget Text Content')
                //->setRole(['USER_INFO_WIDGET'])
                ->setData(function () {
                    return ['userCount' => 5];
                })
                ->setOrder(5)
            );
    }
}
```
#### Step 2: Create Widget Template
You can create a Twig template for the widget or can only use text content.
```twig
# templates/userInfo.html.twig

{% if widget.isActive %}
    <div class="col-lg-3 col-md-4 col-sm-6 col-6">
        <div class="card text-center bg-primary text-white widget_user_info">
            <div class="card-body">
                {# Action Button #}
                {% include '@PdWidget/widgetAction.html.twig' %}

                <span class="count">{{ widget.data.userCount }}</span>
                <h5 class="font-weight-light">{{ 'widget_user_info.count'|trans }}</h5>
            </div>
        </div>
    </div>
{% endif %}
```

#### Step 3: Create Widget Services:
```yaml
# config/services.yaml

# Load All Widgets
App\Widgets\:
    resource: '../src/Widgets/*'
    tags:
        - { name: kernel.event_listener, event: widget.start, method: builder }
        
# Load Single Widget
App\Widgets\DashboardWidget:
    tags:
        - { name: kernel.event_listener, event: widget.start, method: builder }
```

Rendering Widgets
---
The creation process is very simple. You should use widget groups for widget creation.

```twig
# Render all 'admin' widget groups
{{ pd_widget_render('admin') }}

# Render selected widgets in 'admin' group
{{ pd_widget_render('admin', ['user_info']) }}
```


