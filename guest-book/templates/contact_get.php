<section>
    <h2>
        Leave a public Note /Question
    </h2>


    <form method="POST">
        <!-- CSRF -->

        <input type="hidden" name="csrfToken" value="<?=$data['csrfToken']?>" />

        <label for="name">
            Name
        </label>
        <input type="text"  name="name"/>

        <label for="email">
            Email
        </label>
        <input type="email"  name="email"/>

        <label for="message">
            Message
        </label>
        <textarea rows="4"   name="message">

        </textarea>

        <button type="submit">
            Send Message
        </button>
    </form>
</section>