{% verbatim %}
<div id="app">
  <div>
    <form class="pure-form pure-form-stacked">
      <div>
        <label for="inputMargin">Margin: {{ thumbMargin }}</label>
        <input type="range" min="0" max="20" v-model="thumbMargin" id="inputMargin">
      </div>
      <div>
        <label for="inputScale">Scale: {{ thumbScale }}</label>
        <input type="range" min="0.1" max="5" step="0.01" v-model="thumbScale" id="inputScale">
      </div>
      <button v-on:click="confirm" class="pure-button pure-button-primary">Update</button>
    </form>
    <section class="gallery-container">
      <div v-for="img in imgs" v-bind:style="getThumbStyle(img)" class="thumb-container">
        <i v-bind:style="{'padding-bottom': (img.height / img.width * 100) + '%'}" class="thumb-background"></i>
        <img v-bind:src="img.urlThumb"
             v-on:click="showImage($event, img)" alt="" class="thumb">
      </div>
    </section>
    <div id="image-modal" v-bind:class="{'active': !!currentImage}">
      <div v-if="!!currentImage">
        <div v-on:click="previousImage" class="image-ctrl image-ctrl-prev">
          <!--<i class="fa fa-chevron-left"></i>-->
          <strong style="font-size: 4rem">&lt;</strong>
        </div>
        <div v-if="!!currentImage">
          <h3 v-if="currentImage.title">{{currentImage.title}}</h3>
          <img v-bind:src="currentImage.url"
               v-on:click="hideImage" alt="">
        </div>
        <div v-on:click="nextImage" class="image-ctrl image-ctrl-next">
          <!--<i class="fa fa-chevron-right"></i>-->
          <strong style="font-size: 4rem">&gt;</strong>
        </div>
        <div v-on:click="hideImage" class="image-ctrl image-ctrl-close">
          <!--<i class="fa fa-close"></i>-->
          <strong style="font-size: 4rem">&times;</strong>
        </div>
        <div v-if="currentImage.description" class="image-description">
          <p>
            {{currentImage.description}}
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
{% endverbatim %}
