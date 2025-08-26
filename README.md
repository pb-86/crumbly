# Crumbly
Simple and lightweight [WordPress](https://wordpress.org/) plugin for theme developers that provide easy to use function for displaying breadcrumbs.

Please keep in mind that this is my very first WordPress plugin. I will make every effort to ensure that it is constantly developed. All comments and advice are of course welcome.

## How to use it
To display breadcrumbs on your page using **Crumbly** plugin, you have to simply add following line of code in your WordPress theme. Inserting it to the header.php file will be the best way.
```php
<?php if ( function_exists( 'crumbly' ) ) { crumbly(); } ?>
```

## What HTML code does the plugin generate?
In short, this is the case:
```html
<nav class="crumbly" aria-label="breadcrumb">
  <ol class="crumbly__list" itemscope="" itemtype="http://schema.org/BreadcrumbList">
    <li class="crumbly__item" itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem">
      <a class="crumbly__link" href="" itemprop="item" itemtype="http://schema.org/Thing">
        <span itemprop="name">Home page</span>
      </a>
      <span class="crumbly__separator">&raquo;</span>
      <meta itemprop="position" content="1">
    </li>
    <li class="crumbly__item  crumbly__item--active" aria-current="page" itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem">
      <a class="crumbly__link" href="" itemprop="item" itemtype="http://schema.org/Thing">
        <span itemprop="name">Link</span>
      </a>
      <meta itemprop="position" content="2">
    </li>
  </ol>
</nav>
```

## Add your CSS
In default, **Crumbly** will only print your breadcrumbs in form of ordered list. To make your breadcrumbs look great, you have to add some CSS styles.

So, I recommend to start with following CSS:
```css
.crumbly {
  /* This is breadcrumbs container */
}
.crumbly__list {
  /* This is the class that is added to the <ol> tag */
}
.crumbly__item {
  /* This is the class that is added to the <li> tag */
}
.crumbly__item--active{
  /* This is the class that is added to the last item of the breadcrumbs */
}
.crumbly__link {
  /* This is the class that is added to all the links */
}
.crumbly__separator {
  /* This is the class that is added to separator container tag */
}
```

or if you prefer [Sass](https://sass-lang.com/) with [BEM](http://getbem.com/) methodology:
```scss
// This is breadcrumbs container
.crumbly {
  &__list {
    // This is the class that is added to the <ol> tag
  }
  &__item {
    // This is the class that is added to the <li> tag
    &--active {
      // This is the class that is added to the last item of the breadcrumbs
    }
  }
  &__link {
    // This is the class that is added to all the links
  }
  &__separator {
  /* This is the class that is added to separator container tag */
  }
}
```
## Compatibility note 

### Function name

The main function has been renamed from `rddgbc()` to `crumbly()`. A backward-compatible wrapper `rddgbc()` is still provided and simply calls `crumbly()`, so existing themes or plugins using `rddgbc()` will continue to work. New themes should call `crumbly()` directly.

### CSS class names

In older versions the plugin used the legacy prefix `rddgbc__` for CSS classes (for example `rddgbc__list`, `rddgbc__item`). Since this release the classes use the `crumbly__` prefix (for example `crumbly__list`, `crumbly__item`).  

If your theme's styles stopped working, update your stylesheet by replacing `rddgbc__` with `crumbly__`, to maintain backward compatibility.