# api_caso_estudio


Technologies:
Framework: Symphony 5
Database: Postgres

# Como instalar el api
```
1-composer install 
2-php bin/console doctrine:database:create
3-php bin/console doctrine:schema:update --force
4-php bin/console cache:clear
 ```

#  Como generar un usuario
```
php bin/console app:add-user
1-mail 
2-password
3-confirm password
```

#  Como ejecutar la aplicación
```
php -S localhost:8000 -t public/
```
 
#  Endpoint para autenticarse
```
http://127.0.0.1:8000/api/login_check
``` Parametros: ```
{"email":"admin@admin.com","password":"123"}

```Respuesta: ```
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```
```
Lista de Endpoints
```
```
1-[GET] http://127.0.0.1:8000/api/products
  Descripcion: Endpoint que lista todos los productos
  Parametros:
```
```
2-[POST] http://127.0.0.1:8000/api/add
  Descripcion: Endpoint que crea un nuevo producto
  Parametros: {"name":"Hola","price":"234","category":"pepe","description":"","additionalInformation":"","score":"","amount":"25"}
```
```
3-[PUT] http://127.0.0.1:8000/api/update
  Descripcion: Endpoint que qctualiza un nuevo producto dado
  Parametros: {"id":"2", "name":"Hola","price":"234","category":"pepe","description":"","additionalInformation":"","score":"","amount":"25"}
```
```
4-[GET] http://127.0.0.1:8000/api/get/product/1
  Descripcion: Endpoint que obtiene los valores de un producto dado su id
  Parametros: {"name":"","price":"","category":"","description":"","additionalInformation":"","score":"","sku":""}
```
```
5-[DELETE] http://127.0.0.1:8000/api/delete/1
  Descripcion: Endpoint que elimina un producto dado su id
  Parametros: 
```
```
6-[POST] http://127.0.0.1:8000/api/full_search
  Descripcion: Endpoint que entregue resultados de una búsqueda enviando cualquiera de las características del producto, una o varias (permitir paginación en base de 10 resultados). Si no se envía ninguna característica el resultado debe ser la lista de artículos paginada
  Parametros: Cualquiera de los siguientes parametros: {"id":"","name":"","price":"","category":"","description":"","additionalInformation":"","score":"","sku":"", "page":"1","limit":"2" }
```
```
7-[POST] http://127.0.0.1:8000/api/custom_search
  Descripcion: Endpoint que entregue sólo la cantidad de resultados de una búsqueda enviando cualquiera de las características del producto.
  Parametros: Cualquiera de los siguientes parametros: {"id":"","name":"","price":"","category":"","description":"","additionalInformation":"","score":"","sku":""}
```
```
8-[POST] http://127.0.0.1:8000/api/sell
  Descripcion: Endpoint que permita vender
  Parametros: {"product_id":"5"}
```
```
9-[GET] http://127.0.0.1:8000/api/sold_products
  Descripcion: Endpoint que permita mostrar la lista de artículos vendidos.
  Parametros: 
```
```
10-[GET] http://127.0.0.1:8000/api/total_gain
  Descripcion: Endpoint que permita mostrar la ganancia total.
  Parametros:
```
```
11-[GET] http://127.0.0.1:8000/api/products_not_stock
  Descripcion: Endpoint que permita mostrar los artículos que no tienen stock en el almacén
  Parametros: 
```




