<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print get_admin_page_title() ?></h1>
<table class="widefat fixed table">
    <!--thead>
        <tr>
            <th><?php print __('Project','coders_repository') ?></th>
            <th><?php print __('Status','coders_repository') ?></th>
        </tr>
    </thead-->
    <tbody class="projects">
        <?php foreach( $this->list_projects  as $id => $project) : ?>
        <tr>
            <td>
                <a class="project" href="<?php print $this->url($id) ?>" target="_self">
                    <?php print $this->display_image($project['image_id']) ?>
                </a>
                <!--div class="project">
                        <?php print $this->display_image($project['image_id']) ?>
                        <a class="title" href="<?php print $this->url($id) ?>" target="_self"><?php print $project['title'] ?></a>
                </div-->
            </td>
            <td class="container">
                <div class="container solid centered">
                    <h2>
                        <a class="link" href="<?php print $this->url($id) ?>" target="_self">
                            <?php print $project['title'] ?>
                        </a>
                    </h2>
                    <h4><?php print $this->status($project['status']) ?></h4>
                    <h4><?php printf('%s Subscribers', $project['subscribers']) ?></h4>
                    <?php print $this->display_progress_bar($project['progress']) ?>
                </div>
                <i class="control bottom pad-sm right"><?php printf('%s: %s',
                                __('Created', 'coders_repository'),
                                $project['date_created']) ?></i>
                </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <!--tfoot>
        <tr>
            <td colspan="2">
                
            </td>
        </tr>
    </tfoot-->
</table>
<ul class="projects widefat fixed">
    <?php foreach( $this->list_projects  as $id => $project) : ?>
    <li>
    </li>
    <?php endforeach; ?>
</ul>