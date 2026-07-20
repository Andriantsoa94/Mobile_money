# Todo

### Version 1

**Operateur**
- [] Configuration prefixe valable (033 ,037)
- [] Creation type d'operation (depot ,retrait ,transfert)
  - Bareme voir dans le docs de Mr
- [] Gain via fraits
- [] Situation comptes clients

**Client**
- [] Login auto avec telephone (pas d'inscription)
- [] operation
  - voir solde
  - faire depot
  - faire retrait
  - faire transfert
  - voir l'historique

**Base**
- user(nom, CIN, role, idNumero)
- role(type) => client , admin
- numero(numero ,idClient)

- prefixe(num ,idOperateur) => valide sa tsia
- operateur(nom)
- typeOperation(libelle ,boolean)
- config(min ,max ,gain)
- transaction(operation ,gain ,user ,datetime)
- solde(idUser ,value)

**À faire**
- operateur  
  - user admin
  - dashboard
    - 
  - crud prefixes
  - type d'operation (+ config)
  - config frais operation
  - situation gains
  - situation compte clients
- clients
  - login numero
    - dashboard
      - 
  - solde , depot ,transfert ,retrait ,voir historique

- role (opérateur, clients)
  - par rapport a son numero (faire table user (numero ,type))
- faire la config pour n'avoir que les (033, 037)
- 