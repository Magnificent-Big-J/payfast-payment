
 <form action="<?= $url ?>" method="POST">
        <?php foreach ($formFields as $name => $value): ?>
            <input type="hidden" name="<?= $name ?>" value="<?= $value ?>">
        <?php endforeach; ?>
     <button type="submit"  class="paygate-button"
             style="background-color: #083360; color: #fff; padding: 10px 20px; border: none; border-radius: 10px; font-size: 14px; cursor: pointer;"
     >Make Payment</button>
 </form>