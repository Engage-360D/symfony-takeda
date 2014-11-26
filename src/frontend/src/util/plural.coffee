module.exports = plural = (n, a, b, c) =>
  if n % 10 is 1 and n % 100 isnt 11
    a
  else if n % 10 >= 2 and n % 10 <= 4 and (n % 100 < 10 or n % 100 >= 20)
    b
  else
    c
