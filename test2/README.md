# API Specification

## Validate Excel File

Request :
- Method : POST
- Endpoint : `/validate-excel`
- Body(Form Data) :
``` 
   file : {Excel File}" 
```
- Response :
```json 
{
    "error" : "int"
    "messages" : "string"
}
```
