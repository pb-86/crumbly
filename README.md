# Crumbly
Simple and lightweight [WordPress](https://wordpress.org/) plugin for theme developers that provide easy to use function for displaying breadcrumbs.

Please keep in mind that this is my very first WordPress plugin. I will make every effort to ensure that it is constantly developed. All comments and advice are of course welcome.

## How to use it
To display breadcrumbs on your page using **Crumbly** plugin, you have to simply add following line of code in your WordPress theme. Inserting it to the header.php file will be the best way.
```php
<?php if ( function_exists( 'rddgbc' ) ) { rddgbc(); } ?>
```

## What HTML code does the plugin generate?
In short, this is the case:
```html
<nav class="rddgbc" aria-label="breadcrumb">
  <ol class="rddgbc__list" itemscope="" itemtype="http://schema.org/BreadcrumbList">
    <li class="rddgbc__item" itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem">
      <a class="rddgbc__link" href="" itemprop="item" itemtype="http://schema.org/Thing">
        <span itemprop="name">Home page</span>
      </a>
      <span class="rddgbc__separator">&raquo;</span>
      <meta itemprop="position" content="1">
    </li>
    <li class="rddgbc__item  rddgbc__item--active" aria-current="page" itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem">
      <a class="rddgbc__link" href="" itemprop="item" itemtype="http://schema.org/Thing">
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
.rddgbc {
  /* This is breadcrumbs container */
}
.rddgbc__list {
  /* This is the class that is added to the <ol> tag */
}
.rddgbc__item {
  /* This is the class that is added to the <li> tag */
}
.rddgbc__item--active{
  /* This is the class that is added to the last item of the breadcrumbs */
}
.rddgbc__link {
  /* This is the class that is added to all the links */
}
.rddgbc__separator {
  /* This is the class that is added to separator container tag */
}
```

or if you prefer [Sass](https://sass-lang.com/) with [BEM](http://getbem.com/) methodology:
```scss
// This is breadcrumbs container
.rddgbc {
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
