<!-- File: media.video.php -->
<!-- Template: media.video -->
<templateSetting caption="Video Settings" order="1">
    <dl class="sparkDialog _tpl-box">
        <dt>Embed URL</dt>
        <dd>
            <input type="text" name="custom_src" value="https://www.youtube.com/embed/dQw4w9WgXcQ" class="form-control">
            <small class="form-text text-muted">Paste an embed URL from YouTube, Vimeo, or another video provider.</small>
        </dd>
    </dl>
    <dl class="sparkDialog _tpl-box">
        <dt>Title</dt>
        <dd><input type="text" name="custom_title" value="Featured Video" class="form-control"></dd>
    </dl>
    <dl class="sparkDialog _tpl-box">
        <dt>Caption</dt>
        <dd><textarea name="custom_caption" rows="2" class="form-control">Add an optional caption for additional context.</textarea></dd>
    </dl>
    <dl class="sparkDialog _tpl-box">
        <dt>Alignment</dt>
        <dd class="align-options">
            <label><input type="radio" name="custom_align" value=" text-start"> Left</label>
            <label><input type="radio" name="custom_align" value=" text-center" checked> Center</label>
            <label><input type="radio" name="custom_align" value=" text-end"> Right</label>
        </dd>
    </dl>
</templateSetting>
<div class="{custom_align}">
    <figure class="video-figure" data-tpl-tooltip="Video">
        <div class="video-block">
            <iframe src="{custom_src}" title="{custom_title}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>
        </div>
        <figcaption class="video-caption" data-editable>{custom_caption}</figcaption>
    </figure>
</div>
