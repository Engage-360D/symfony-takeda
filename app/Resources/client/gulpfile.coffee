path = require "path"
gulp = require "gulp"
rename = require "gulp-rename"
browserify = require "gulp-browserify"
coffeeify = require "coffeeify"
reactify = require "reactify"


webDirectory = path.join __dirname, "..", "..", "..", "web"


gulp.task "scripts", ->
  gulp.src("src/index.coffee", read: false)
    .pipe(browserify({
      transform: [
        coffeeify,
        reactify
      ],
      extensions: [".coffee"]
    }))
    .pipe(rename("frontend.js"))
    .pipe(gulp.dest(webDirectory))


gulp.task "watch", ->
  gulp.watch "src/**/*.coffee", ["scripts"]


gulp.task "default", [
  "scripts"
  "watch"
]
