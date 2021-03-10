'use strict';

(function () {
  'use strict';
    /**
    * Service Worker Toolbox caching
    */

    var cacheVersion = '-toolbox-v1';
    var dynamicVendorCacheName = 'dynamic-vendor' + cacheVersion;
    var staticVendorCacheName = 'static-vendor' + cacheVersion;
    var staticAssetsCacheName = 'static-assets' + cacheVersion;
    var contentCacheName = 'content' + cacheVersion;
    var maxEntries = 50;
    //以下的网址请更换为博客地址(可以填写绝对链接或者相对链接)
    self.importScripts('https://www.fyvdc.com/sw-toolbox.js');
    self.toolbox.options.debug = false;
    //由于我的博客启用Autoptimize，以及WP Super Cache，所以添加Cache目录
    self.toolbox.router.get('wp-content/cache/(.*)', self.toolbox.cacheFirst, {
        cache: {
          name: staticAssetsCacheName,
          maxEntries: maxEntries
        }
    });
    //添加毒瘤jquery的缓存规则
    self.toolbox.router.get('wp-includes/js/jquery/jquery.js', self.toolbox.cacheFirst, {
        cache: {
          name: staticAssetsCacheName,
          maxEntries: maxEntries
        }
    });
    //添加主题的静态资源，具体目录请自行更换
    self.toolbox.router.get('/wp-content/themes/grace-minimal-theme/assets/(.*)', self.toolbox.cacheFirst, {
        cache: {
          name: staticAssetsCacheName,
          maxEntries: maxEntries
        }
    });
    //以下均为第三方资源缓存
    self.toolbox.router.get('/(.*)', self.toolbox.cacheFirst, {
        origin: /cdn\.bootcss\.com/,
        cache: {
          name: staticVendorCacheName,
          maxEntries: maxEntries
        }
    });

    self.toolbox.router.get('/(.*)', self.toolbox.cacheFirst, {
        origin: /static\.yecdn\.com/,
        cache: {
          name: staticVendorCacheName,
          maxEntries: maxEntries
        }
    });
    
    // 缓存 googleapis
    self.toolbox.router.get('/css', self.toolbox.fastest, {
        origin: /fonts\.googleapis\.com/,
            cache: {
              name: dynamicVendorCacheName,
              maxEntries: maxEntries
            }
    });
    
    self.toolbox.router.get('/css', self.toolbox.fastest, {
        origin: /fonts\.yecdn\.com/,
            cache: {
              name: dynamicVendorCacheName,
              maxEntries: maxEntries
            }
    });

    self.toolbox.router.get('/(.*)', self.toolbox.cacheFirst, {
        origin: /(fonts\.gstatic\.com|www\.google-analytics\.com)/,
        cache: {
          name: staticVendorCacheName,
          maxEntries: maxEntries
        }
    });
    
    self.toolbox.router.get('/(.*)', self.toolbox.cacheFirst, {
        origin: /(fonts-gstatic\.yecdn\.com|www\.google-analytics\.com)/,
        cache: {
          name: staticVendorCacheName,
          maxEntries: maxEntries
        }
    });

    // immediately activate this serviceworker
    self.addEventListener('install', function (event) {
        return event.waitUntil(self.skipWaiting());
    });

    self.addEventListener('activate', function (event) {
        return event.waitUntil(self.clients.claim());
    }); 

})();
