<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Coba Template</title>
    <script>
      /**
       * This script is used to track user interactions and page views.
       * It is only for demo purposes. Don't use it in production.
       */
      !function (t, e) { var o, n, p, r; e.__SV || (window.posthog = e, e._i = [], e.init = function (i, s, a) { function g(t, e) { var o = e.split("."); 2 == o.length && (t = t[o[0]], e = o[1]), t[e] = function () { t.push([e].concat(Array.prototype.slice.call(arguments, 0))) } } (p = t.createElement("script")).type = "text/javascript", p.crossOrigin = "anonymous", p.async = !0, p.src = s.api_host.replace(".i.posthog.com", "-assets.i.posthog.com") + "/static/array.js", (r = t.getElementsByTagName("script")[0]).parentNode.insertBefore(p, r); var u = e; for (void 0 !== a ? u = e[a] = [] : a = "posthog", u.people = u.people || [], u.toString = function (t) { var e = "posthog"; return "posthog" !== a && (e += "." + a), t || (e += " (stub)"), e }, u.people.toString = function () { return u.toString(1) + ".people (stub)" }, o = "init capture register register_once register_for_session unregister unregister_for_session getFeatureFlag getFeatureFlagPayload isFeatureEnabled reloadFeatureFlags updateEarlyAccessFeatureEnrollment getEarlyAccessFeatures on onFeatureFlags onSessionId getSurveys getActiveMatchingSurveys renderSurvey canRenderSurvey getNextSurveyStep identify setPersonProperties group resetGroups setPersonPropertiesForFlags resetPersonPropertiesForFlags setGroupPropertiesForFlags resetGroupPropertiesForFlags reset get_distinct_id getGroups get_session_id get_session_replay_url alias set_config startSessionRecording stopSessionRecording sessionRecordingStarted captureException loadToolbar get_property getSessionProperty createPersonProfile opt_in_capturing opt_out_capturing has_opted_in_capturing has_opted_out_capturing clear_opt_in_out_capturing debug".split(" "), n = 0; n < o.length; n++)g(u, o[n]); e._i.push([i, s, a]) }, e.__SV = 1) }(document, window.posthog || []);
      const currentDomain = window.location.hostname;
      if (["docs.tabler.io", "preview.tabler.io", "dev.tabler.io"].includes(currentDomain)) {
        posthog.init("phc_Ui9ZadLvY1XWtP5iiD3LWDZoXAGYsz6yNvTAvE7wSwn", {
          api_host: `/eat`,
          person_profiles: "identified_only",
          capture_pageview: true,
          capture_pageleave: true,
          loaded: function (posthog) {
            console.log("PostHog initialized on", currentDomain);
          },
        });
      }
    </script>
    <meta name="msapplication-TileColor" content="#066fd1" />
    <meta name="theme-color" content="#066fd1" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="HandheldFriendly" content="True" />
    <meta name="MobileOptimized" content="320" />
    <link rel="icon" href="./favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />
    <meta
      name="description"
      content="Tabler is packed with beautifully crafted components and powerful features. Jump in and start building a stunning dashboard — all for free!"
    />
    <meta name="canonical" content="https://preview.tabler.io/sign-in-illustration.html" />
    <meta property="og:image" content="https://preview.tabler.io/static/og.png" />
    <meta property="og:image:width" content="1280" />
    <meta property="og:image:height" content="640" />
    <meta property="og:site_name" content="Tabler" />
    <meta property="og:type" content="object" />
    <meta property="og:title" content="Tabler: Premium and Open Source dashboard template with responsive and high quality UI." />
    <meta property="og:url" content="https://preview.tabler.io/static/og.png" />
    
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <!-- <link href="./dist/css/tabler.min.css?1759774799" rel="stylesheet" integrity="sha384-kz+I4+mczbNiZfLAJMxOlJaZmnbRYhARHNkR2k6tal4gz7OL33/0puDD3SvkiNX9" /> -->
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PLUGINS STYLES -->
    <!-- <link
      href="./dist/css/tabler-flags.min.css?1759774799"
      rel="stylesheet"
      integrity="sha384-kmvP0hkBXZ2hMSZlbvE1Q2HIXzPCQRL3ijUeqNiwaPd2nl2Aks+s3gW+V5fAHOX9"
    />
    <link
      href="./dist/css/tabler-socials.min.css?1759774799"
      rel="stylesheet"
      integrity="sha384-eWmz8gyiLzrDw3JcT/PJxjGyKizQjvByfHqocjrMMkIrbKFCnOuP/qMwAz3bHmsC"
    />
    <link
      href="./dist/css/tabler-payments.min.css?1759774799"
      rel="stylesheet"
      integrity="sha384-xWIXbKxPLGG/ZEGUKxDjJn3xmUgd2PC2CSZUKJ4PyTse49DiuvJx2WT5wSNJRyw9"
    />
    <link
      href="./dist/css/tabler-vendors.min.css?1759774799"
      rel="stylesheet"
      integrity="sha384-+X7+c/noY2B9ieq9daEaVStkUhIFyJTO5T6Occ6jZisx57sbECetvloLqcvGahUv"
    />
    <link
      href="./dist/css/tabler-marketing.min.css?1759774799"
      rel="stylesheet"
      integrity="sha384-4dAlYnPzCom9yeC/5++PFq2FG/szJRlUPsDSrjZ3EWP8IAzK7g7rrsnSfqrS67Se"
    />
    <link
      href="./dist/css/tabler-themes.min.css?1759774799"
      rel="stylesheet"
      integrity="sha384-jTe/MdN6BlY4S3eYe6Qw++yTjuezmVnxWp/l7GAG1qXGC+jttphHqsAN/bGPvJOk"
    /> -->
    <!-- END PLUGINS STYLES -->
    <!-- BEGIN CUSTOM FONT -->
    <style>
      @import url("https://rsms.me/inter/inter.css");
    </style>
    <!-- END CUSTOM FONT -->
    @vite('resources/js/app.js')
    @inertiaHead
  </head>
  <body>
    @inertia()
  </body>
</html>