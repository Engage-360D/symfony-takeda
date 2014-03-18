args   = require("optimist").argv
gulp = require "gulp"
gutil = require "gulp-util"
gulpif = require "gulp-if"
rename = require "gulp-rename"
browserify = require "gulp-browserify"
coffeeify = require "coffeeify"
reactify = require "reactify"
uglify = require "gulp-uglify"
jade = require "gulp-jade"
stylus = require "gulp-stylus"
concat = require "gulp-concat"
path = require "path"

isProduction = args.type is "production"
target = path.resolve args.target or "./../../../web/bundles"
pkg = require "./package.json"

gulp.task "views", ->
  gulp.src("./admin.jade")
    .pipe(jade())
    .pipe(gulp.dest("#{target}/../"))

gulp.task "scripts", ->
  gulp.src(["./admin.coffee"], read: false)
    .pipe(browserify({
      transform: [
        "coffeeify",
        "reactify"
      ],
      shim: pkg.shim,
      extensions: [".coffee"],
      debug: !isProduction
    }))
    .pipe(rename("./admin.js"))
    .pipe(gulp.dest(target))

gulp.task "styles", ->
  gulp.src("./components/**/**/**/*.styl")
    .pipe(
      stylus(
        use: ["nib"], 
        set: ["include css", true],
        import:[],
        paths:[
          "./node_modules/"
        ]
      )
    )
    .pipe(concat("admin.css"))
    .pipe(gulp.dest(target))

gulp.task "watch", ->
  gulp.watch "./admin.jade", ["views"]
  gulp.watch "./**/*.coffee", ["scripts"]
  gulp.watch "./components/**/**/**/*.styl", ["styles"]

gulp.task "default", ["views", "scripts", "styles", "watch"]
