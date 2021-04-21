<?php
header("HTTP/1.1 503 Service Temporarily Unavailable");
header("Status: 503 Service Temporarily Unavailable");
header("Retry-After: 3600");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>We’re temporarily unavailable.</title>
  <style>
  body {font-family: "Source Sans Pro", sans-serif; font-size: 18px;}
  img.maintenance-logo { width: 120px; vertical-align: middle; display: block;}
  div.content { max-width: 500px; margin: auto; display: block;}
  footer {padding: 20px; background-color: #21295E; color: #ffffff;}
  </style>
</head>
<body>
<article>
  <div class="content">
  <p>
  <a href="/"><img src="/wp-content/uploads/2021/02/brand-1.svg" class="maintenance-logo" alt="safrapay logo" loading="lazy"></a>
  </p>
  <h1>We’re temporarily unavailable.</h1>
    <p>We’ve scheduled a little self-care time to freshen our pixels and tidy our code. We should be ready to greet the world again soon.</p>
    <p>We apologize for any inconvenience. If you need any assistance in the mean time, don’t hesitate to reach out to our lovely Customer Support Team at 1-877-472-3727.</p>
  </div>
</article>
<footer>
  <div class="content">
    <p>© 2021 Safrapay Inc.</p>
  </div>
</footer>
</body>
</html>