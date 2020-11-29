<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print get_admin_page_title() ?></h1>
<table class="widefat fixed table">
    <!--thead>
        <tr>
            <th><?php print __('Project','coders_artpad') ?></th>
            <th><?php print __('Status','coders_artpad') ?></th>
        </tr>
    </thead-->
    <tbody class="projects">
        <tr>
            <td>
                <h2><?php print __('Dashboard','coders_artpad') ?></h2>
            </td>
            <td>
                <div class="panel right inline">
                    <?php print $this->display_new(__('New project title','coders_artpad')) ?>
                    <?php print $this->action_create(__('Create','coders_artpad'),'button-primary'); ?>
                </div>
            </td>
        </tr>
        <?php foreach( $this->list_projects  as $id => $project) : ?>
        <tr>
            <td>
                <a class="project" href="<?php print $this->project_url($id) ?>" target="_self">
                    <?php print $this->display_image($project['image_id']) ?>
                </a>
            </td>
            <td class="container">
                <a class="link" href="<?php print $this->project_url($id) ?>" target="_self">
                    <h2 class="title center pad-sm">
                        <?php print $project['title'] ?>
                    </h2>
                </a>
                    <div class="container solid centered">
                    <h4><?php print $this->status($project['status']) ?></h4>
                    <h4><?php printf('%s Subscribers', $project['subscribers']) ?></h4>
                    <?php print $this->display_progress_bar($project['progress']) ?>
                    <a href="<?php print $this->public_url($id) ?>" target="_blank"><?php print __('Public','coders_artpad') ?></a>
                </div>
                <i class="control bottom pad-sm right"><?php printf('%s: %s',
                                __('Created', 'coders_artpad'),
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