<div class="pjs-filter__controls_main">
  <label class="pjs-filter__control pjs-filter__control_type_text">
    <span class="pjs-filter__control__label">
      What
    </span>

    <input
      type="text"
      placeholder="Job title, keywords, or company"
      minlength="2"
      class="pjs-filter__control__input"
      v-model="controls.main.searchTerm.val"
    >

    <span class="pjs-filter__control__icon-wrapper">
      {icon}
    </span>
  </label>

  <label class="pjs-filter__control pjs-filter__control_type_text">
    <span class="pjs-filter__control__label">
      Where
    </span>

    <input
      type="text"
      placeholder="<?php echo esc_attr( 'City, state, zip code, or "remote"' ); ?>"
      class="pjs-filter__control__input"
      v-model="controls.main.location.val"
    >

    <span class="pjs-filter__control__icon-wrapper">
      {icon}
    </span>
  </label>

  <button type="button" class="pjs-filter__submit" @click="() => search()">
    Search
  </button>

  <div class="pjs-filter__tip">
    {Tip Text}
  </div>
</div>