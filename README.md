### Fonctionnalités List :

- ✅ En tant qu’utilisateur non connecté, je peux consulter la liste des magasins près de chez moi
    `/magasins/near?latitude=46.2&longitude=5.5 dans MagasinController`
- ✅ En tant qu’utilisateur non connecté, je peux consulter les produits vendus dans ces magasins
    `/magasins/stock/{id} dans MagasinController`
- ✅ En tant qu'utilisateur non connecté, je peux consulter si un produit est disponible en stock
    `/produits/find/{id-product} dans ProduitController`
- ✅ En tant qu’utilisateur non connecté, je peux écrire un message à un vendeur
-   `/message dans MessageController`
- ✅ En tant qu'utilisateur non connecté, je peux m'inscrire afin de devenir un client
  `/users dans UtilisaeurController`
- ✅En tant que client, je peux commander un ou plusieurs produits dans un magasin
  `/commandes dans CommandeController`
- En tant que client, je dispose d'une liste de créneau disponible pour récupérer ma commande
- En tant que client, je souhaite pouvoir réserver un créneau pour récupérer ma commande.
- En tant qu’administrateur, je peux déclarer de nouvelles boutiques et mettre à jour le stock en ajoutant ou retirant des articles
- En tant qu’administrateur, je peux notifier par mail mes clients que leur commande est validée.

### Fait en plus :
- ✅ En tant qu’utilisateur non connecté, je peux consulter la liste de tous les magasins
    `/magasins/all dans MagasinController`
- ✅ En tant qu’utilisateur non connecté, je peux consulter la liste de tous les produits
    `/produits/all dans ProduitController`


### Rooting List :
> GET
```
/magasins/all
/magasins/near?latitude=46.2&longitude=5.5
/magasins/stock/{id}

/produits/all
/produits/find/{id-product}

/users

/commandes/all
```
> POST
```
/users
->  {
    "email": "newuser@example.com",
    "password": "motdepasse123",
    "nom":"John",
    "prenom":"Doe",
    "type":1
}

/login
-> {
    "email": "newuser@example.com",
    "password": "motdepasse123"
}


```
### Entity List :
- Magasin
- Produit
- ...