<?php

namespace App\Controller\App;


use App\Entity\Calling\Calling;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use EasyCorp\Bundle\EasyAdminBundle\Factory\FilterFactory;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\HttpFoundation\StreamedResponse;


use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;





class CallingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Calling::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('deleted')
            ->add('price')
            ->add('status')
            ->add('partner')
            ->add('admin')
            ->add('doctor')
            ->add(DateTimeFilter::new('createdAt'))
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('numberCalling'),
           // TextField::new('title'),
            TextField::new('name'),
            TextField::new('address'),
            TextField::new('status'),
           // TextField::new('description'),
            //TextField::new('chronicDiseases'),
            TextField::new('nosology'),
            TextField::new('age'),
            TextField::new('leadType'),
          //  TextField::new('partnerName'),
            TextField::new('rejectedComment'),
            DateTimeField::new('createdAt'),
          //  DateTimeField::new('acceptedAt'),
          //  DateTimeField::new('completedAt'),
            AssociationField::new('admin'),
            AssociationField::new('doctor'),
            AssociationField::new('partner'),
            IntegerField::new('price'),
            IntegerField::new('estimated'),
            IntegerField::new('prepayment'),
            IntegerField::new('coastHospital'),
           // TextField::new('price'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewInvoice = Action::new('Скачать excel')
            ->linkToCrudAction('downloadExcel')
            ->createAsGlobalAction();

        $actions
            ->add(Crud::PAGE_INDEX, $viewInvoice)
            ->disable(Action::NEW)
            ->disable(Action::DELETE)
            ->disable(Action::BATCH_DELETE)
        ;
        return $actions;
    }

    public function downloadExcel(AdminContext $context)
    {


        $event = new BeforeCrudActionEvent($context);
        $this->container->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

        $fields = FieldCollection::new($this->configureFields(Crud::PAGE_INDEX));
        $context->getCrud()->setFieldAssets($this->getFieldAssets($fields));
        $filters = $this->container->get(FilterFactory::class)->create($context->getCrud()->getFiltersConfig(), $fields, $context->getEntity());
        $queryBuilder = $this->createIndexQueryBuilder($context->getSearch(), $context->getEntity(), $fields, $filters);

        $callings = $queryBuilder->getQuery()->getResult();

        $headers = [
            'Номер',
            'Имя',
            'Адрес',
            'Партнер',
            'Создан',
            'Завершен',
            'Цена',
        ];

        $entities = [];
        /** @var Calling $calling */
        foreach ($callings as $calling){
            $entities[] = [
              'number' => $calling->getNumberCalling(),
              'name' => $calling->getName(),
              'address' => $calling->getAddress(),
              'partner' => $calling->getPartner()?->getName(),
              'created' => $calling->getCreatedAt()?->format('d.m.y'),
              'completed' => $calling->getCompletedAt()?->format('d.m.y'),
              'price' => $calling->getPrice() ?? 0,
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;
        foreach ($headers as $i => $header) {
            $sheet->setCellValueByColumnAndRow(++$i, $row, $header);
        }

        $styleArray = [
            'font'  => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ]];

        $spreadsheet->getActiveSheet()
            ->getStyle(Coordinate::stringFromColumnIndex(1) .'1:'. Coordinate::stringFromColumnIndex(count($headers)) .'1')
            ->applyFromArray($styleArray)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('066885')
        ;

        foreach ($entities as $entry) {
            $row++;
            $values = array_values($entry);

            foreach ($values as $i => $value) {
                $sheet->setCellValueByColumnAndRow(++$i, $row, $value);
            }
        }

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $spreadsheet->setActiveSheetIndex($spreadsheet->getIndex($worksheet));

            $sheet = $spreadsheet->getActiveSheet();
            $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);

            foreach ($cellIterator as $cell) {
                $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
            }
        }

        $sheet->setTitle('Вызовы');

        $streamedResponse = new StreamedResponse();

        $streamedResponse->setCallback(function () use ($spreadsheet) {
            $writer =  new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $streamedResponse->setStatusCode(200);
        $streamedResponse->headers->set('Content-Type', 'application/vnd.ms-excel');
        $streamedResponse->headers->set('Content-Disposition', 'attachment; filename="callings.xlsx"');

        return $streamedResponse->send();
    }
}
