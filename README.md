# About
`FastParser.php` contain function `Parse(fastXmlContent)` which converting large fast xml-file to short like this:

```json
{
    "date": {
        "hour":   23,
        "minute": 55,
        "second": 0,
        "day":    2,
        "month":  3,
        "year":   2017
    },
    "version": "1.8.36",
    "build": 311,
    "players":
    [
        {
            "id": 0,
            "license": 0
        },
        {
            "id": 1,
            "first_name": "first",
            "last_name":  "last"
        }
    ],
    "tournaments":
    [
        {
            "name": "Open 01.03.2017",
            "type": "AMATEUR",
            "begin_date": {
                "day": 1,
                "month": 3,
                "year": 2017
            },
            "end_date": {
                "day": 1,
                "month": 3,
                "year": 2017
            },
            "country": "UKR",
            "teams":
            [
                {
                    "id": 0,
                    "players": [ 0, 1 ]
                }
            ],
            "phases":
            [
                {
                    "type": "P",
                    "ranks":
                    [
                        {
                            "team_id": 0
                        }
                    ]
                },
                {
                    "type": "D",
                    "ranks":
                    [
                        {
                            "team_id": 0
                        }
                    ]
                }
            ]
        }
    ]
}
```

# License
The MIT License (MIT)

Copyright © 2017 ALEXANDER BIVZYUK

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and
associated documentation files (the “Software”), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions
of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT
OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
