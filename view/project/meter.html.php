<?php
use Goteo\Library\Text,
    Goteo\Library\Check;

$level = (int) $this['level'] ?: 3;

$horizontal = !empty($this['horizontal']);
$big = !empty($this['big']);
$activable = !empty($this['activable']);

$project = $this['project'];

$minimum    = $project->mincost;
$optimum    = $project->maxcost;
$reached    = $project->invested;
$days       = $project->days;


// PHP la pifia (y mucho) con los cálculos en coma flotante
if ($reached >= $minimum) {
    $minimum_done = floor(($reached / $minimum) * 100);
    $minimum_done_per = floor(($reached / $minimum) * 100);
    $minimum_left = 0;
    
} else {
    
    $minimum_done = min(100, floor(($reached / $minimum) * 100));
    $minimum_done_per = floor(($reached / $minimum) * 100);
    $minimum_left = max(0, floor((1 - $reached / $minimum) * 100));
    
    if ($minimum_done >= 100) {
        // No muestres 100 si falta aunque sea un céntimo
        $minimum_done = 99;
    }
}

if (!$horizontal) {
    // si aun no ha alcanzado el optimo controlamos la visualización para que no confunda
    // $minimum_done es el % de heigth del mercurio
    // si no ha alcanzado el óptimo, el máximo será 120%
    if ($reached < $optimum && $minimum_done > 120)
        $minimum_done = 120;

    // y si es menos del doble del optimo, que se mantenga en 140
    if ($reached > $optimum && $reached <= $optimum*1.5) {
        $minimum_done = 140;
    }

    // y si es menos de 1.5 del optimo, que se mantenga en 140
    if ($reached > $optimum && $reached <= $optimum*1.2) {
        $minimum_done = 135;
    }
}



$more  = $optimum - $minimum;
$over = $reached - $minimum;

if ($over > 0) {
    
    if ($over >= $more) {
        $optimum_done = 100;
    } else {
        $optimum_done = min(100, floor($over / ($optimum - $minimum)));
        
        if ($optimum_done >= 100) {
            $optimum_done = 99;
        }
    }    
    
} else {
    $optimum_done = 0;
}

$optimum_left = 100 - $optimum_done;

$minimum_ratio =  min(100, floor(($minimum / $optimum) * 100));

?>        
    <div class="meter <?php echo $horizontal ? 'hor' : 'ver'; echo $big ? ' big' : ''; echo $activable ? ' activable' : ''; ?>">
        
        <h<?php echo $level ?> class="title investment"><?php echo Text::get('project-view-metter-investment'); ?></h<?php echo $level ?>>
        <?php if (!empty($project->round)) : ?><h<?php echo $level ?> class="title ronda"><?php echo $project->round . Text::get('regular-round'); ?></h<?php echo $level ?>><?php endif; ?>
        <?php if ($activable) : ?><h<?php echo $level ?> class="title obtained"><?php echo Text::get('project-view-metter-got'); ?></h<?php echo $level ?>><?php endif; ?>
        <div class="graph">
            <div class="optimum">
                 <div class="left" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($optimum_left) ?>%"></div>
                 <div class="done" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($optimum_done) ?>%"></div>
            </div>
            <div class="minimum" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_ratio) ?>%">
                <div class="left" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_left) ?>%"><!-- <strong><?php echo number_format($minimum_left) ?>%</strong> --></div>
                <div class="done" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_done) ?>%"><strong><?php echo number_format($minimum_done_per) ?>%</strong></div>
            </div>
        </div>

        <dl>
            <dt class="optimum"><?php echo Text::get('project-view-metter-optimum'); ?></dt>
            <dd class="optimum"><strong><?php echo \amount_format($optimum) ?></strong> <span class="euro">&euro;</span></dd>

            <dt class="minimum" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_ratio) ?>%"><span><?php echo Text::get('project-view-metter-minimum'); ?></span></dt>
            <dd class="minimum" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_ratio) ?>%"><strong><?php echo \amount_format($minimum) ?> <span class="euro">&euro;</span></strong> </dd>

            <dt class="reached"><span><?php echo Text::get('project-view-metter-got'); ?></span></dt>
            <dd class="reached"><strong><?php echo \amount_format($reached) ?> <span class="euro">&euro;</span></strong></dd>

            <?php
            switch ($project->status) {
                case 1: // en edicion
                ?>
            <dt class="days long"><span><?php echo Text::get('project-view-metter-day_created'); ?></span></dt>
            <dd class="days long"><strong><?php echo date('d / m / Y', strtotime($project->created)) ?></strong></dd>
                <?php
                break;

                case 2: // enviado a revision
                ?>
            <dt class="days long"><span><?php echo Text::get('project-view-metter-day_updated'); ?></span></dt>
            <dd class="days long"><strong><?php echo date('d / m / Y', strtotime($project->updated)) ?></strong></dd>
                <?php
                break;

                case 4: // financiado
                case 5: // caso de exito
                ?>
            <dt class="days long"><span><?php echo Text::get('project-view-metter-day_success'); ?></span></dt>
            <dd class="days long"><strong><?php echo date('d / m / Y', strtotime($project->success)) ?></strong></dd>
                <?php
                break;

                case 6: // archivado
                ?>
            <dt class="days long"><span><?php echo Text::get('project-view-metter-day_closed'); ?></span></dt>
            <dd class="days long"><strong><?php echo date('d / m / Y', strtotime($project->closed)) ?></strong></dd>
                <?php
                break;

                default:
                    if ($days > 2) :
                ?>
            <dt class="days"><span><?php echo Text::get('project-view-metter-days'); ?></span></dt>
            <dd class="days"><strong><?php echo number_format($days) ?></strong> <?php echo Text::get('regular-days'); ?></dd>
                <?php
                    else :
                        $part = strtotime($project->published);
                        // si primera ronda: published + 40
                        // si segunda ronda: published + 80
                        $plus = 40 * $project->round;
                        $final_day = date('Y-m-d', mktime(0, 0, 0, date('m', $part), date('d', $part)+$plus, date('Y', $part)));
                        $timeTogo = Check::time_togo($final_day,1);
                ?>
            <dt class="days"><span><?php echo Text::get('project-view-metter-days'); ?></span></dt>
            <dd class="days"><strong><?php echo $timeTogo ?></strong></dd>
                <?php
                    endif;
                break;
            }
            ?>

            <dt class="supporters"><span><?php echo Text::get('project-view-metter-investors'); ?></span></dt>
            <dd class="supporters"><strong><?php echo $project->num_investors ?></strong></dd>

        </dl>

        <?php if ($activable) : ?>
        <div class="obtained">
            <strong><?php echo \amount_format($reached) ?> <span class="euro">&euro;</span></strong>
            <span class="percent"><?php echo number_format($minimum_done_per) ?>%</span>
        </div>
        <?php endif; ?>

    <?php /*
    // si en estado 3 ha alcanzado el optimo o segunda ronda, "aun puedes seguir aportando" junto al quedan tantos días
    if ($project->status == 3 && ($project->round == 2  || $project->amount >= $project->maxcost || ($project->round == 1  && $project->amount >= $project->mincost) )) : ?>
        <div class="keepiton"><?php echo Text::get('regular-keepiton') ?></div>
    <?php endif; */ ?>

    </div> 