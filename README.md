[![Build Status](https://travis-ci.org/joskolenberg/jory.svg?branch=master)](https://travis-ci.org/joskolenberg/jory.svg?branch=master)
[![Total Downloads](https://poser.pugx.org/joskolenberg/jory/downloads)](https://packagist.org/packages/joskolenberg/jory)
[![Latest Stable Version](https://poser.pugx.org/joskolenberg/jory/v/stable)](https://packagist.org/packages/joskolenberg/jory)
[![StyleCI](https://github.styleci.io/repos/147561955/shield?branch=master)](https://github.styleci.io/repos/147561955)
[![Code Coverage](https://codecov.io/gh/joskolenberg/jory/branch/master/graph/badge.svg)](https://codecov.io/gh/joskolenberg/jory/branch/master/graph/badge.svg)
[![License](https://poser.pugx.org/joskolenberg/jory/license)](https://packagist.org/packages/joskolenberg/jory)

# Jory
Jory is a way of defining database queries using a JSON string, useful for loading dynamic data from the front-end. Jory can add high flexibility to your REST API without the need to create e.g. a whole new GraphQL implementation. It can easily be used alongside your existing code.

This package only translates a Jory JSON string into PHP objects, and thus defines the convention for setting up Jory-strings. This convention is explained further in this document.

For the Laravel implementation for setting up Jory endpoints, take a look at the [laravel-jory](https://packagist.org/packages/joskolenberg/laravel-jory) package.

## The basics
A Jory-string is a JSON string consisting of 5 parts, all these parts are optional. When omitting a parameter, the API's default implementation will be applied. 

* [Filtering](#filtering)
* [Sorting](#sorting)
* [Fields](#fields)
* [Relations](#relations)
* [Offset and Limit](#offset-and-limit)

An example of a valid Jory-string for a musicians resource could be:
```json
{
  "filter": {
    "field": "last_name",
    "operator": "like",
    "data": "%Clapton%"
  },
  "sorts": [
    "number_of_no_1_hits",
    "-id"
  ],
  "fields": [
    "id",
    "first_name",
    "last_name"
  ],
  "relations": {
    "bands": {
      "fields": [
        "id",
        "name",
      ]
    }
  },
  "offset": 10,
  "limit": 50
}
```

This example would tell the API to return:
* Only musicians having a last_name value containing "Clapton".
* Order these records by number_of_no_1_hits ascending first, and by id descending second.
* Return only the "id", "first_name" and "last_name" fields for a musician.
* Also return the "bands" relation, but only the "id" and "name" fields.
* Skip the first 10 musicians and return the next 50.

### Parameters
All parameters have a full and minified version. For example; "flt" is the shorthand notation for the "filter" parameter. All shorthand notations will be shown in parentheses after the full name: ```filter (flt)```

## Filtering
A filter can be passed by using the ```filter (flt)``` parameter.

### Single filter
When only applying a single filter, a filter consists of a ```field (f)```, ```operator (o)``` and ```data (d)``` parameter.
The ```operator (o)``` and ```data (d)``` are optional (depening on the implementation). Only passing a ```field (f)``` parameter could be useful when dealing with boolean conditions. When only a ```field (f)``` and ```data (d)``` parameter are passed, a fallback to an "equals" comparison should be implemented by the API.

#### Examples:

Return only the active records:
```json
{
  "filter": {
    "field": "is_active"
  }
}
```

Return only the record with an id of 123 (minified):
```json
{
  "flt": {
    "f": "id",
    "d": 123
  }
}
```

Return only the records with an id greater than 123 (minified):
```json
{
  "flt": {
    "f": "id",
    "o": ">",
    "d": 123
  }
}
```

### Grouped filters
When applying multiple filters you need to choose whether they should by applied in an AND or OR fashion.

This can be done using the ```group_and (and)``` or ```group_or (or)``` parameters. The parameters should contain an array of filters, these filters can be [single filters](#single-filter) or another grouped AND or OR filter if you want to create nested conditions. 

#### Examples:

Return only the records which are active AND are last modified after 6 dec 2018:
```json
{
  "filter": {
    "group_and": [
      {
        "field": "is_active",
        "data": true
      },
      {
        "field": "modified_at",
        "operator": ">",
        "data": "2018-12-06"
      }
    ]
  }
}
```

Return only the records which are active AND are last modified in 2017 AND have a last_name starting with "Clap":
```json
{
  "filter": {
    "group_and": [
      {
        "field": "is_active",
      },
      {
        "group_or": [
          {
            "field": "modified_at",
            "operator": ">=",
            "data": "2017-01-01"
          },
          {
            "field": "modified_at",
            "operator": "<",
            "data": "2018-01-01"
          }
        ]
      },
      {
        "field": "last_name",
        "operator": "like",
        "data": "Clap%"
      }
    ]
  }
}
```

Or minified:
```json
{
  "flt": {
    "and": [
      {
        "f": "is_active",
      },
      {
        "or": [
          {
            "f": "modified_at",
            "o": ">=",
            "d": "2017-01-01"
          },
          {
            "f": "modified_at",
            "o": "<",
            "d": "2018-01-01"
          }
        ]
      },
      {
        "f": "last_name",
        "o": "like",
        "d": "Clap%"
      }
    ]
  }
}
```

## Sorting
The sorting parameter was inspired by the [jsonapi](https://jsonapi.org/format/#fetching-sorting) specification and is pretty easy. Sorting is done using the ```sorts (srt)``` parameter, this parameter holds an array with column names. Sorting is done ascending by default, when a column name is prefixed with a dash (```-```), the sorting will be done descending. The sorts will be applied in the given order.

#### Examples

Order the result by first_name ascending:
```json
{
  "sorts": [
    "first_name"
  ]
}
```

Order the result by last_name ascending first and first_name descending second (minified):
```json
{
  "srt": [
    "last_name",
    "-first_name"
  ]
}
```

## Fields
When you want to retrieve only specific fields other than the default, you can pass the ```fields (fld)``` parameter. This parameter holds an array of the fields to be returned.

#### Examples

Return only the first_name field:
```json
{
  "fields": [
    "first_name"
  ]
}
```

Return only the id, first_name and last_name fields (minified):
```json
{
  "fld": [
    "id",
    "first_name",
    "last_name"
  ]
}
```

## Relations
The relations you want to retrieve for each record are defined using the ```relations (rlt)``` parameter. This parameter holds an object with key/value pairs, the key being the relation name and the value being another Jory object (including optional filtering/sorting etc. on the loaded relation). This gives you the possibility to easily load unlimited nested relations with maximum flexibility.

#### Examples

Load the musicians last named Clapton including the related bands.
```json
{
  "filter": {
    "field": "last_name",
    "operator": "=",
    "data": "Clapton"
  },
  "relations": {
    "bands": {}
  }
}
```

Load the musicians last named Clapton including the related bands and the title of songs he has written before 1980 ordered by release date descending.
```json
{
  "filter": {
    "field": "last_name",
    "operator": "=",
    "data": "Clapton"
  },
  "relations": {
    "bands": {},
    "songs_written": {
      "filter": {
        "field": "release_date",
        "operator": "<",
        "data": "2018-01-01"
      },
      "sorts": [
        "-release_date"
      ],
      "fields": [
        "title"
      ]
    }
  }
}
```

Or minified:
```json
{
  "flt": {
    "f": "last_name",
    "o": "=",
    "d": "Clapton"
  },
  "rlt": {
    "bands": {},
    "songs_written": {
      "flt": {
        "f": "release_date",
        "o": "<",
        "d": "2018-01-01"
      },
      "srt": [
        "-release_date"
      ],
      "fld": [
        "title"
      ]
    }
  }
}
```
When you want to load multiple levels of relations you can also use dot-notation.
```json
{
  "rlt": {
    "bands.songs": {
      "fld": ["title"]
    },
    "bands.albums": {
      "fld": ["name"]
    }
  }
}
```
Equals:
```json
{
  "rlt": {
    "bands": {
      "rlt": {
      	"songs": {
          "fld": ["title"]
      	},
      	"albums": {
      	  "fld": ["name"]
      	}
      }
    }
  }
}
```

## Offset and Limit
The offset and limit can be applied using the ```offset (ofs)``` and ```limit (lmt)``` parameter. These parameters should hold an integer value.

#### Examples

Return the first 50 records:
```json
{
  "limit": 50
}
```

Return record 11 to 30 (minified):
```json
{
  "ofs": 10,
  "lmt": 20
}
```


That's it! Any suggestions or issues? Please contact me!

Happy coding!

Jos Kolenberg <joskolenberg@gmail.com>
