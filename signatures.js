var fs=require('fs');
var obj=require('./signatures.json');
function forEach(obj, fn, path) {
    if (typeof obj == "object")
        Object.keys(obj).forEach(function(key) {
            var deepPath = path ? path + '.' + key : key;
            // Note that we always use obj[key] because it might be mutated by forEach
            if (fn.call(obj, obj[key], key, obj, deepPath))
                forEach(obj[key], fn, deepPath);
        })
}

 var table = [];
    forEach(obj, function(thing, key, obj, path) {
        var thingy;
        if (thing.email) {
            thingy={"include_vars":thing}
            thingy.timestamp=thing.timestamp
            table.push(thingy)
            return false
        } else {
            return true
        }
    });
fs.writeFile("signatures.json",JSON.stringify(table),"UTF-8",function(a){a&&console.log(a)})