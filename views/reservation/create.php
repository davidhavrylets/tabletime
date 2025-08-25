<h2>Reserver une table</h2>
<form action="?route=reservation/create" method="POST">
    <div>
        <label for="date">Дата:</label>
        <input type="date" name="date" required>
    </div>
    <div>
        <label for="time">Время:</label>
        <input type="time" name="time" required>
    </div>
    <div>
        <label for="guests">Количество гостей:</label>
        <input type="number" name="guests" min="1" required>
    </div>
    <button type="submit">Забронировать</button>
</form>