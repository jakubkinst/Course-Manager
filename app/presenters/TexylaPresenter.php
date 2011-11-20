<?php
class TexylaPresenter extends Presenter {

        public function actionPreview($texy) {
                $texyInstance = new Texy;
                echo $texyInstance->process($texy);
                $this->terminate();
        }

}
?>