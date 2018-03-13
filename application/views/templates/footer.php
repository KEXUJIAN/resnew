    </div>
    <?php if ($display ?? true): ?>
    <footer class="navbar navbar-default navbar-fixed-bottom">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="javasricpt:;">Simple Resource Management Web System</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="javasricpt:;">KE, XUJIAN <i class="fa fa-copyright"></i> 2017, License: MIT </a>
                </li>
                <?php $user = App::getUser();?>
                <?php if ($user && \Res\Model\User::ROLE_MANAGER !== $user->role()): ?>
                <li>
                    <a class="service" target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=1689118050&site=qq&menu=yes">
                       <i class="fa fa-qq"> 联系管理员</i>
                    </a>
                </li>
                <?php endif;?>
                <li style="display: none;">
                    <a href="http://codeigniter.org.cn/" target="_blank">Powered by <i class="fa fa-fire"></i> CodeIgniter</a>
                </li>
                <li style="display: none;">
                    <a href="http://fontawesome.io" target="_blank">Font Awesome <i class="fa fa-font-awesome"></i> by Dave Gandy</a>
                </li>
            </ul>
        </div>
    </footer>
    <?php endif; ?>
</body>
</html>
