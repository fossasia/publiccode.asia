var fs = require('fs');
var obj = require('./signatures.json');

function signDigest(text)
{
if(window.event)
window.event.cancelBubble = true;

var dest = sign(text); //TODO
alert(dest);
return dest;
}

// CAPICOM constants
var CAPICOM_STORE_OPEN_READ_ONLY = 0;
var CAPICOM_CURRENT_USER_STORE = 2;
var CAPICOM_CERTIFICATE_FIND_SHA1_HASH = 0;
var CAPICOM_CERTIFICATE_FIND_EXTENDED_PROPERTY = 6;
var CAPICOM_CERTIFICATE_FIND_TIME_VALID = 9;
var CAPICOM_CERTIFICATE_FIND_KEY_USAGE = 12;
var CAPICOM_DIGITAL_SIGNATURE_KEY_USAGE = 0x00000080;
var CAPICOM_AUTHENTICATED_ATTRIBUTE_SIGNING_TIME = 0;
var CAPICOM_INFO_SUBJECT_SIMPLE_NAME = 0;
var CAPICOM_ENCODE_BASE64 = 0;
var CAPICOM_E_CANCELLED = -2138568446;
var CERT_KEY_SPEC_PROP_ID = 6;

function IsCAPICOMInstalled()
{
if(typeof(oCAPICOM) == "object")
{
if( (oCAPICOM.object != null) )
{
// We found CAPICOM!
return true;
}
}
}

function FindCertificateByHash()
{

try
{
// instantiate the CAPICOM objects
var MyStore = new ActiveXObject("CAPICOM.Store");
// open the current users personal certificate store
MyStore.Open(CAPICOM_CURRENT_USER_STORE, "My", CAPICOM_STORE_OPEN_READ_ONLY);

// find all of the certificates that have the specified hash
var FilteredCertificates = MyStore.Certificates.Find(CAPICOM_CERTIFICATE_FIND_SHA1_HASH, strUserCertigicateThumbprint);

var Signer = new ActiveXObject("CAPICOM.Signer");
Signer.Certificate = FilteredCertificates.Item(1);
return Signer;

// Clean Up
MyStore = null;
FilteredCertificates = null;
}
catch (e)
{
if (e.number != CAPICOM_E_CANCELLED)
{
return new ActiveXObject("CAPICOM.Signer");
}
}
}

function sign(src)
{
if(window.crypto &amp;&amp; window.crypto.signText)
return sign_NS(src);

return sign_IE(src);
}

function sign_NS(src)
{
var s = crypto.signText(src, "ask" );
return s;
}

function sign_IE(src)
{
try
{
// instantiate the CAPICOM objects
var SignedData = new ActiveXObject("CAPICOM.SignedData");
var TimeAttribute = new ActiveXObject("CAPICOM.Attribute");

// Set the data that we want to sign
SignedData.Content = src;
var Signer = FindCertificateByHash();

// Set the time in which we are applying the signature
var Today = new Date();
TimeAttribute.Name = CAPICOM_AUTHENTICATED_ATTRIBUTE_SIGNING_TIME;
TimeAttribute.Value = Today.getVarDate();
Today = null;
Signer.AuthenticatedAttributes.Add(TimeAttribute);

// Do the Sign operation
var szSignature = SignedData.Sign(Signer, true, CAPICOM_ENCODE_BASE64);
return szSignature;
}
catch (e)
{
if (e.number != CAPICOM_E_CANCELLED)
{
alert("An error occurred when attempting to sign the content, the errot was: " + e.description);
}
}
return "";
}
function forEach(obj, fn, path) {
    if (obj && typeof obj == "object")
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
        thingy = {
            "include_vars": thing
        }
        thingy.timestamp = thing.timestamp
        table.push(thingy)
        return false
    } else {
        return true
    }
});
fs.writeFile("signatures.json", JSON.stringify(table), "UTF-8", function(a) {
    a && console.log(a)
})
