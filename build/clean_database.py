#!/usr/bin/env python3
import json
import sys

injson = str(sys.argv[1])
outjson = str(sys.argv[2])

def remove_error_info(d):
    if not isinstance(d, (dict, list)):
        return d
    if isinstance(d, list):
        return [remove_error_info(v) for v in d]
    return {k: remove_error_info(v) for k, v in d.items()
            if k not in {'email', 'code', 'permPriv', 'permNews' }}

with open(injson) as json_data:
    sigs_full = json.load(json_data)

sigs_clean = remove_error_info(sigs_full)

with open(outjson, 'w') as outfile:
    json.dump(sigs_clean, outfile, indent=2)
