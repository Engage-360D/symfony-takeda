path = require "path"
gulp = require "gulp"
rename = require "gulp-rename"
concat = require "gulp-concat"
browserify = require "gulp-browserify"
stylus = require "gulp-stylus"
coffeeify = require "coffeeify"
reactify = require "reactify"


webDirectory = path.join __dirname, "..", "..", "..", "web"
pkg = require "./package.json"


gulp.task "scripts", ->
  gulp.src("src/index.coffee", read: false)
    .pipe(browserify(
      transform: [
        coffeeify
        reactify
      ]
      extensions: [".coffee"],
      shim: pkg.shim
    ))
    .pipe(rename("frontend.js"))
    .pipe(gulp.dest(webDirectory))


gulp.task "styles", ->
  gulp.src("src/**/*.styl")
    .pipe(stylus(
      use: ["nib"]
      set: ["include css", true]
      paths:[
        "./node_modules/"
      ]
    ))
    .pipe(concat("frontend.css"))
    .pipe(gulp.dest(webDirectory))


gulp.task "watch", ->
  gulp.watch "src/**/*.coffee", ["scripts"]
  gulp.watch "src/**/*.styl", ["styles"]


gulp.task "default", [
  "scripts"
  "styles"
  "watch"
]
