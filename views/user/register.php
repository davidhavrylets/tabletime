<h2>Регистрация</h2>
<form action="?route=register" method="POST">
    <div>
        <label for="nom">Nom:</label>
        <input type="text" name="nom" required>
    </div>
    <div>
        <label for="prenom">Prénom:</label>
        <input type="text" name="prenom" required>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" required>
    </div>
    <div>
        <label for="telephone">Téléphone:</label>
        <input type="text" name="telephone">
    </div>
    <div>
        <label for="mot_de_passe">Mot de passe:</label>
        <input type="password" name="mot_de_passe" required>
    </div>
    <button type="submit">S’inscrire</button>
</form>