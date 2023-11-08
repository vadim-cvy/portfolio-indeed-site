<div class="pjs-filter__main-controls">
  <label class="pjs-filter__main-controls__control">
    <span class="pjs-filter__main-controls__control__label">
      What
    </span>

    <input
      type="text"
      placeholder="Job title, keywords, or company"
      minlength="2"
      class="pjs-filter__main-controls__control__input"
      @change="e => updateControlVal( controls.searchTerm, e.target.value )"
    >

    <span class="pjs-filter__main-controls__control__icon-wrapper">
      {icon}
    </span>
  </label>

  <label class="pjs-filter__main-controls__control">
    <span class="pjs-filter__main-controls__control__label">
      Where
    </span>

    <input
      type="text"
      placeholder="<?php echo esc_attr( 'City, state, zip code, or "remote"' ); ?>"
      class="pjs-filter__main-controls__control__input"
      @change="e => updateControlVal( controls.location, e.target.value )"
    >

    <span class="pjs-filter__main-controls__control__icon-wrapper">
      {icon}
    </span>

    <div class="pjs-filter__main-controls__control__tip">
      {Tip Text}
    </div>
  </label>

  <button type="button" class="pjs-filter__submit" @click="() => search()">
    Search
  </button>
</div>