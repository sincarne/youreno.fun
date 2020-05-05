# youreno.fun
A simple PHP script to create static HTML for a directory of images

## Rationale
I used to have a bare directory listing (customised using `.htaccess`) for my collection of gifs and reaction images at (youreno.fun)[http://youreno.fun]. Then I wrote a PHP script so I could supply `og:image` information, and have the images expand automatically in messaging and blogging apps. That worked fine, but was a bit slow on my shared hosting. So now I generate an `index.html`, and one `html` file for each image. Run the file with a `cron` job, or manually whenever you update the directory.
