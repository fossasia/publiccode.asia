var secret = '<secret here>'

function ObjectToArray2D(json) {
    var keys = {}
    var name = Object.keys(json);

    function setKeys(obj) {
        Object.keys(obj).forEach(function(key) {
            keys[key] = obj
        })
    }
    Object.keys(json).forEach(function(key) {
        setKeys(json[key])
    });
    return [
        ["Keys\\Names"].concat(name)
    ].concat(Object.keys(keys).map(function(key) {
        return [key].concat(name.map(function(named) {
            return json[named][key]
        }))
    }))
}

function forEach(obj, fn, path) {
    if (obj&&typeof obj == "object")
        Object.keys(obj).forEach(function(key) {
            var deepPath = path ? path + '.' + key : key;
            // Note that we always use obj[key] because it might be mutated by forEach
            if (fn.call(obj, obj[key], key, obj, deepPath))
                forEach(obj[key], fn, deepPath);
        })
}

function getFirebaseUrl(jsonPath) {
    /*
    We then make a URL builder
    This takes in a path, and
    returns a URL that updates the data in that path
    */
    return 'https://publiccodeasia.firebaseio.com/' + jsonPath + '.json?auth=' + secret
}

function syncMasterSheet(excelData) {
    /*
    We make a PUT (update) request,
    and send a JSON payload
    More info on the REST API here : https://firebase.google.com/docs/database/rest/start
    */
    var options = {
        method: 'put',
        contentType: 'application/json',
        payload: JSON.stringify(excelData)
    };
    var fireBaseUrl = getFirebaseUrl('subscribers')

    /*
    We use the UrlFetchApp google scripts module
    More info on this here : https://developers.google.com/apps-script/reference/url-fetch/url-fetch-app
    */
    UrlFetchApp.fetch(fireBaseUrl, options);
}

function readFirebase() {
    var options = {
        method: 'GET'
    };
    var fireBaseUrl = getFirebaseUrl('subscribers');
    var sheet = SpreadsheetApp.getActiveSheet();
    var js;
    var table = {};
    forEach(JSON.parse(js = UrlFetchApp.fetch(fireBaseUrl, options)), function(thing, key, obj, path) {
        if (thing.email) {
            table[path] = thing;
            return false
        } else {
            return true
        }
    });
    table = ObjectToArray2D(table);
    var range = sheet.getRange(1, 1, table.length, table[0].length);
    // Logger.log(range.getValues())
    range.setValues(table)
    Logger.log(js);

}

function pathObjectSet(path, obj, val) {
    var paths = path.split('.');
    for (var i = 0, currobj = obj, pat = paths[i]; i < paths.length - 1; i++, pat = paths[i]) {
        if (!currobj[pat]) {
            currobj[pat] = {}
        }
        currobj = currobj[pat]
    }
    currobj[pat] = val;
    return obj
}

function f2DArraytoObject(arr) {
    var obj = {}
    var flatobj = {}
    var objlist = [];
    var verticali = 0
    arr[verticali++].slice(1).forEach(function(key) {
        objlist.push(key);
        flatobj[key] = {};
        pathObjectSet(key, obj, flatobj[key])
    })
    arr.slice(1).forEach(function(varr) {
        objlist.forEach(function(asd, t) {
            flatobj[asd][varr[0]] = varr.slice(1)[t]
        })
    })
    return obj
}

function writeFirebase() {
    //Get the currently active sheet
    var sheet = SpreadsheetApp.getActiveSheet();
    //Get the number of rows and columns which contain some content
    var [rows, columns] = [sheet.getLastRow(), sheet.getLastColumn()];
    //Get the data contained in those rows and columns as a 2 dimensional array
    var data = sheet.getRange(1, 1, rows, columns).getValues();

    //Use the syncMasterSheet function defined before to push this data to the "masterSheet" key in the firebase database
    syncMasterSheet(f2DArraytoObject(data));
}